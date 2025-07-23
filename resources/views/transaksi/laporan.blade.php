<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
<h3 style="text-align: center; ">
  {{ $pengaturan->nama_toko }}
</h3>

<table>
  <tr>
    <td>
      <small><b>Alamat</b></small>
    </td>
    <td>
      <small>{{$pengaturan->alamat}}</small>
    </td>
  </tr>
  <tr>
    <td>
      <small><b>Telepon</b></small>
    </td>
    <td>
      <small>{{$pengaturan->telepon}}</small>
    </td>
  </tr>
  <tr>
    <td>
      <small><b>E-Mail</b></small>
    </td>
    <td>
      <small>{{$pengaturan->email}}</small>
    </td>
  </tr>
</table>
<hr>
<h5 style="text-align: center;">Laporan Transaksi Tanggal {{ $dari }} s.d. {{ $sampai }}</h5><br />
<div class="table-responsive">
  <table class="table table-striped">
    <tr>
      <td width="30%">
        Total Transaksi
      </td>
      <td id="totalTransaksi">
        {{ $totalTransaksi }}
      </td>
    </tr>
    <tr>
      <td>
        Buku Terjual
      </td>
      <td id="bukuTerjual">
        {{$bukuTerjual->buku_terjual ? $bukuTerjual->buku_terjual : '0'  }}
      </td>
    </tr>
    <tr>
      <td>
        Total Pendapatan
      </td>
      <td id="totalPendapatan">
        Rp {{number_format($pendapatan, 2, ',', '.')}}
      </td>
    </tr>
  </table>
</div>
