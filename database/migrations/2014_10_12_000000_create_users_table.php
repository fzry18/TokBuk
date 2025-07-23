<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateUsersTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    // Drop enum type if exists, then recreate it
    DB::statement("DROP TYPE IF EXISTS posisi_enum");
    DB::statement("CREATE TYPE posisi_enum AS ENUM ('Owner', 'Admin', 'Operator', 'Kasir')");

    Schema::create('users', function (Blueprint $table) {
      $table->bigIncrements('id');
      $table->string('name');
      $table->string('username');
      $table->string('email');
      $table->string('telepon');
      $table->text('alamat');
      $table->string('password');
      $table->timestamps();
      $table->rememberToken();
    });

    // Add enum column using raw SQL for PostgreSQL
    DB::statement('ALTER TABLE users ADD COLUMN posisi posisi_enum DEFAULT \'Kasir\'');
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('users');
    DB::statement("DROP TYPE IF EXISTS posisi_enum");
  }
}
