<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormulirPendaftaranETable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::defaultStringLength(191);

    Schema::create('formulir_pendaftaran_e', function (Blueprint $table) {
      $table->uuid('user_id')->primary();
      $table->string('nama_kontak')->nullable();
      $table->string('hubungan')->nullable();            
      $table->string('alamat_kontak')->nullable();      
      $table->string('nomor_hp')->nullable();
      
      $table->timestamps();

      $table->foreign('user_id')
        ->references('id')
        ->on('users')
        ->onDelete('cascade')
        ->onUpdate('cascade');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('formulir_pendaftaran_e');
  }
}
