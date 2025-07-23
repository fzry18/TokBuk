<?php

namespace App\Http\Controllers;

use App\Pengadaan;
use App\Transaksi;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade as PDF;
use App\Pengaturan;
use App\Retur;

class LaporanController extends Controller
{
  private $now;

  public function __construct()
  {
    $this->now = Carbon::now();
  }

  public function index()
  {
    $akhirBulan = $this->now->daysInMonth;
    return view('laporan.index', compact('akhirBulan'));
  }

  public function pdfTransaksi($dari, $sampai)
  {
    // Suppress all warnings and deprecation errors for DOMPDF
    $old_error_reporting = error_reporting(0);
    ini_set('display_errors', 0);

    try {
      $transaksi = Transaksi::whereDate('transaksi.created_at', '>=', $dari)->whereDate('transaksi.created_at', '<=', $sampai);

      $totalTransaksi = $transaksi->count();
      $pendapatan = $transaksi->sum('total_harga');
      $bukuTerjual = $transaksi->join('detail_transaksi as dt', 'dt.id_transaksi', '=', 'transaksi.id')
        ->select(DB::raw('SUM(dt.jumlah) as buku_terjual'))
        ->first();

      $dari = Carbon::parse($dari)->format('d-m-Y');
      $sampai = Carbon::parse($sampai)->format('d-m-Y');
      $pengaturan = Pengaturan::first();

      // Start output buffering to capture any warnings
      ob_start();
      $pdf = PDF::loadView('transaksi.laporan', compact('totalTransaksi', 'pendapatan', 'bukuTerjual', 'pengaturan', 'dari', 'sampai'))->setPaper('a4', 'potrait');
      ob_end_clean(); // Clear any output buffer

      // Restore error reporting
      error_reporting($old_error_reporting);
      ini_set('display_errors', 1);

      return $pdf->download('laporan_transaksi_' . $dari . '_' . $sampai . '.pdf');
    } catch (\Exception $e) {
      // Restore error reporting in case of exception
      error_reporting($old_error_reporting);
      ini_set('display_errors', 1);

      throw $e;
    }
  }

  public function pdfPengadaan($dari, $sampai)
  {
    // Suppress all warnings and deprecation errors for DOMPDF
    $old_error_reporting = error_reporting(0);
    ini_set('display_errors', 0);

    try {
      $pengadaan = Pengadaan::whereDate('pengadaan.tanggal', '>=', $dari)->whereDate('pengadaan.tanggal', '<=', $sampai);

      $totalPengadaan = $pengadaan->count();
      $pengeluaran = $pengadaan->sum('total_harga');
      $bukuTerbeli = $pengadaan->join('detail_pengadaan as dp', 'dp.id_pengadaan', '=', 'pengadaan.id')
        ->select(DB::raw('SUM(dp.jumlah) as buku_terbeli'))
        ->get()
        ->reduce(function($total, $jumlah) {
          return $total + $jumlah->buku_terbeli;
        });

      $dari = Carbon::parse($dari)->format('d-m-Y');
      $sampai = Carbon::parse($sampai)->format('d-m-Y');
      $pengaturan = Pengaturan::first();

      // Start output buffering to capture any warnings
      ob_start();
      $pdf = PDF::loadView('pengadaan.laporan', compact('totalPengadaan', 'pengeluaran', 'bukuTerbeli', 'pengaturan', 'dari', 'sampai'))->setPaper('a4', 'potrait');
      ob_end_clean(); // Clear any output buffer

      // Restore error reporting
      error_reporting($old_error_reporting);
      ini_set('display_errors', 1);

      return $pdf->download('laporan_pengadaan_' . $dari . '_' . $sampai . '.pdf');
    } catch (\Exception $e) {
      // Restore error reporting in case of exception
      error_reporting($old_error_reporting);
      ini_set('display_errors', 1);

      throw $e;
    }
  }
}
