<?php

namespace App\Http\Controllers;

use App\Buku;
use App\DetailPengadaan;
use App\Exports\PengadaanExport;
use App\Distributor;
use App\Pengadaan;
use App\Pengaturan;
use App\Traits\RiwayatAktivitas;
use App\Helpers\PdfHelper;
use Error;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade as PDF;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;

use function React\Promise\reduce;

class PengadaanController extends Controller
{
  use RiwayatAktivitas;

  private function getPengadaanBuilder(): Builder
  {
    return Pengadaan::join('detail_pengadaan as dp', 'dp.id_pengadaan', '=', 'pengadaan.id')
      ->select([
        'pengadaan.id',
        'pengadaan.kode',
        'pengadaan.tanggal',
        'pengadaan.id_distributor',
        'pengadaan.bayar',
        'pengadaan.total_harga',
        'pengadaan.faktur',
        DB::raw('SUM(dp.jumlah) as jumlah_buku')
        ])
        ->groupBy([
        'pengadaan.id',
        'pengadaan.kode',
        'pengadaan.tanggal',
        'pengadaan.id_distributor',
        'pengadaan.bayar',
        'pengadaan.faktur',
        'pengadaan.total_harga'
      ])
      ->orderByDesc(DB::raw('CAST(pengadaan.tanggal as date)'));
  }

  public function index()
  {
    $pengadaan = $this->getPengadaanBuilder()->get();
    $distributor = Distributor::all();

    if ($_GET) {
      return $this->filter(compact('distributor'));
    }

    return view('pengadaan.index', compact('pengadaan', 'distributor'));
  }

  public function filter($data)
  {
    $pengadaan = $this->getPengadaanBuilder();

    if ( $mulai = $_GET['mulai'] ) {
      $pengadaan->whereDate('tanggal', '>=', $mulai);
    }

    if ( $sampai = $_GET['sampai'] ) {
      $pengadaan->whereDate('tanggal', '<=', $sampai);
    }

    if ( $distributor = $_GET['distributor'] ) {
      $pengadaan->where('id_distributor', $distributor);
    }

    if ( $faktur = $_GET['faktur'] ) {
      if ( $faktur == 'Sudah Diunggah' ) {
        $pengadaan->whereNotNull('faktur');
      } else {
        $pengadaan->whereNull('faktur');
      }
    }

    session($_GET);

    $pengadaan = $pengadaan->get();
    $distributor = $data['distributor'];

    return view('pengadaan.index', compact('pengadaan', 'distributor'));
  }

  public function create(Request $request)
  {
    $distributor = Distributor::all();
    return view('pengadaan.create', compact('distributor'));
  }

  public function detail($id)
  {
    $pengadaan = Pengadaan::find($id);
    return view('pengadaan.detail', compact('pengadaan'));
  }

  public function store(Request $request)
  {
    $this->validatePengadaan($request);

    $hargaBeliRequest = $request->hargaBeli;
    $bukuYangDibeliRequest = json_decode($request->bukuYangDibeli);
    $idDistributorRequest = $request->idDistributor;

    if ($hargaBeliRequest < $bukuYangDibeliRequest->totalHarga) {
      return redirect()->route('pengadaan.create')->withErrors(['hargaBeli' => 'Nominal pembayaran untuk membeli pasokan buku dibawah kurang']);
    }

    DB::beginTransaction();

    try {
      $kode = $this->getKodePengadaan();
      $faktur = $request->file('faktur');

      if ( $faktur ) {
        $namaFaktur = $kode . '.' . $faktur->getClientOriginalExtension();
        Storage::disk('public')->put('images/faktur/' . $namaFaktur, file_get_contents($faktur));
      }

      $pengadaan = Pengadaan::create([
        'kode' => $kode,
        'tanggal' => $request->tanggal,
        'faktur' => $faktur ? $namaFaktur : null,
        'id_distributor' => (int) $idDistributorRequest,
        'total_harga' => $bukuYangDibeliRequest->totalHarga,
        'bayar' => $hargaBeliRequest,
        'keterangan' => $request->keterangan
      ]);

      $this->createDetailPengadaan($pengadaan, $bukuYangDibeliRequest);

      DB::commit();

      $this->rekamAktivitas('Membuat pengadaan ' . $kode);

      return redirect()->route('pengadaan.detail', ['id' => $pengadaan->id])->with([
        'type' => 'success',
        'message' => 'Pengadaan Berhasil Dilakukan.'
      ]);
    } catch (Exception $e) {
      DB::rollBack();
      throw new Error($e);
      return redirect()->route('pengadaan.create')->with([
        'type' => 'danger',
        'message' => 'Gagal Melakukan Pengadaan, Silahkan coba lagi.'
      ]);
    }
  }

  private function validatePengadaan(Request $request)
  {
    $request->validate([
      'faktur' => 'max:2048',
      'hargaBeli' => 'required',
      'idDistributor' => 'required'
    ], [
      'faktur.max' => 'Ukuran file terlalu besar, maksimal 2 MB',
      'hargaBeli.required' => 'Mohon masukan harga beli untuk pengadaan ini',
      'idDistributor.required' => 'Mohon pilih distributor'
    ]);
  }

  private function createDetailPengadaan($pengadaan, $bukuYangDibeliRequest)
  {
    foreach ($bukuYangDibeliRequest->buku as $buku) {
      $bukuLama = Buku::where('isbn', $buku->isbn);

      if ( !$bukuLama->count() ) {
        $bukuBaru = Buku::create([
          'sampul' => 'sampul.png',
          'isbn' => $buku->isbn,
          'judul' => $buku->judul,
          'jumlah' => $buku->jumlah
        ]);

        DetailPengadaan::create([
          'id_pengadaan' => $pengadaan->id,
          'id_buku' => $bukuBaru->id,
          'harga' => (int) $buku->harga,
          'jumlah' => $buku->jumlah
        ]);
      } else {
        DetailPengadaan::create([
          'id_pengadaan' => $pengadaan->id,
          'id_buku' => $bukuLama->first()->id,
          'harga' => (int) $buku->harga,
          'jumlah' => $buku->jumlah
        ]);

        if ( $pengadaan->faktur ) {
          $bukuLama->update(['jumlah' => $bukuLama->first()->jumlah + $buku->jumlah]);
        }
      }
    }
  }

  private function getKodePengadaan()
  {
    $jumlahPengadaan = Pengadaan::count() + 2;
    $kodeTerakhir = Pengadaan::latest()->first();
    $kodeTerakhir = $kodeTerakhir ? $kodeTerakhir->kode : 'P00001';
    return substr($kodeTerakhir, 0, -count(str_split((string) $jumlahPengadaan))) . $jumlahPengadaan;
  }

  public function destroy($id)
  {
    DB::beginTransaction();
    try {
      $pengadaan = Pengadaan::find($id);
      $kode = $pengadaan->kode;

      if ( $pengadaan->faktur ) {
        Storage::disk('public')->delete('images/faktur/' . $pengadaan->faktur);
        $this->kembalikanJumlahBuku($pengadaan->detail);
      }
      $pengadaan->delete();

      DB::commit();

      $this->rekamAktivitas('Menghapus pengadaan ' . $kode);

      return redirect()->route('pengadaan')->with([
        'message' => 'Berhasil Menghapus Pengadaan',
        'type' => 'success'
      ]);
    } catch (Exception $e) {
      DB::rollBack();
      throw new Error($e);
      return redirect()->route('pengadaan')->with([
        'message' => 'Gagal Menghapus Pengadaan',
        'type' => 'danger'
      ]);
    }
  }

  private function kembalikanJumlahBuku($detailPengadaan)
  {
    foreach ($detailPengadaan as $detail) {
      $buku = Buku::find($detail->id_buku);
      if (!!$buku) {
        if ($buku->jumlah < $detail->jumlah) {
          $buku->update(['jumlah' => 0]);
        } else {
          $buku->update(['jumlah' => $buku->jumlah - $detail->jumlah]);
        }
      }
    }
  }

  public function export(Request $request)
  {
    return Excel::download(new PengadaanExport($request->mulai, $request->sampai, $request->distributor), 'pengadaan.xlsx');
  }

  public function faktur($id)
  {
    $pengadaan = Pengadaan::find($id);
    return Storage::download('images/faktur/' . $pengadaan->faktur);
  }

  public function laporan($id)
  {
    // Set memory dan error handling untuk PDF generation
    ini_set('memory_limit', '512M');
    ini_set('max_execution_time', 300);
    $old_error_reporting = error_reporting(0);
    ini_set('display_errors', 0);

    try {
      $pengadaan = Pengadaan::find($id);
      $pengaturan = Pengaturan::first();

      ob_start();
      $pdf = PDF::loadView('pengadaan.faktur', compact('pengadaan', 'pengaturan'))
        ->setOptions([
          'isHtml5ParserEnabled' => true,
          'isRemoteEnabled' => false,
          'defaultFont' => 'DejaVu Sans'
        ]);
      ob_end_clean();

      // Restore error reporting
      error_reporting($old_error_reporting);
      ini_set('display_errors', 1);

      return $pdf->download('laporan_' . $pengadaan->kode . '.pdf');
    } catch (\Exception $e) {
      // Restore error reporting in case of exception
      error_reporting($old_error_reporting);
      ini_set('display_errors', 1);
      throw $e;
    }
  }

  public function cetak($id)
  {
    $pengadaan = Pengadaan::find($id);
    $pengaturan = Pengaturan::first();
    return view('pengadaan.cetak', compact('pengadaan', 'pengaturan'));
  }

  public function unggahFaktur(Request $request, $id)
  {
    DB::beginTransaction();

    try {
      $pengadaan = Pengadaan::find($id);

      if ( $pengadaan ) {
        $faktur = $request->file('faktur');

        if ( $faktur ) {
          $namaFaktur = $pengadaan->kode . '.' . $faktur->getClientOriginalExtension();
          Storage::disk('public')->put('images/faktur/' . $namaFaktur, file_get_contents($faktur));

          if ( $pengadaan->update(['faktur' => $namaFaktur]) ) {
            foreach ( $pengadaan->detail as $detail ) {
              $buku = Buku::find($detail->id_buku);
              if ( !!$buku ) {
                $buku->update(['jumlah' => $buku->jumlah + $detail->jumlah]);
              }
            }

            DB::commit();

            return redirect()
              ->route('pengadaan.detail', ['id' => $pengadaan->id])
              ->with([
                'type' => 'success',
                'message' => 'Faktur Berhasil Diunggah'
              ]);
          }
        }
      }
    } catch ( Exception $error ) {
      DB::rollBack();
      throw new Error($error);
      return redirect()
        ->route('pengadaan.detail', ['id' => $pengadaan->id])
        ->with([
          'type' => 'danger',
          'message' => 'Gagal mengunggah faktur, silakan coba lagi'
        ]);
    }
  }
}
