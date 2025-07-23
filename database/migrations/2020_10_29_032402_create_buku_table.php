<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBukuTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('buku', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->string('sampul')->nullable();
      $table->string('isbn');
      $table->string('judul');
      $table->integer('tahun_terbit')->nullable(); // Changed from year to integer
      $table->unsignedBigInteger('id_penulis')->nullable();
      $table->foreign('id_penulis')->references('id')->on('penulis');
      $table->unsignedBigInteger('id_penerbit')->nullable();
      $table->foreign('id_penerbit')->references('id')->on('penerbit');
      $table->unsignedBigInteger('id_kategori')->nullable();
      $table->foreign('id_kategori')->references('id')->on('kategori');
      $table->unsignedBigInteger('id_lokasi')->nullable();
      $table->foreign('id_lokasi')->references('id')->on('lokasi');
      $table->integer('harga')->nullable();
      $table->integer('diskon')->nullable();
      $table->integer('jumlah'); // Changed from string to integer
      $table->text('barcode')->nullable();
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('buku');
  }
}
