<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormulirPendaftaranCTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::defaultStringLength(191);

    Schema::create('formulir_pendaftaran_c', function (Blueprint $table) {
      $table->uuid('user_id')->primary();
      $table->string('nama_ayah')->nullable();
      $table->enum('hubungan',['AYAH_KANDUNG','AYAH_SAMBUNG','AYAH_WALI'])->default('AYAH_KANDUNG');
      $table->string('tempat_lahir')->nullable();
      $table->date('tanggal_lahir')->nullable();
      $table->tinyInteger('idagama')->nullable();

      $table->string('address1_desa_id')->nullable();
      $table->string('address1_kelurahan')->nullable();
      $table->string('address1_kecamatan_id')->nullable();
      $table->string('address1_kecamatan')->nullable();
      $table->string('address1_kabupaten_id')->nullable();
      $table->string('address1_kabupaten')->nullable();
      $table->string('address1_provinsi_id')->nullable();
      $table->string('address1_provinsi')->nullable();
      $table->string('alamat_tempat_tinggal')->nullable();
      $table->integer('kewarganegaraan')->nullable();
      $table->string('nomor_hp')->nullable();

      $table->string('email')->nullable();
      $table->string('pendidikan')->nullable();
      $table->string('pekerjaan_instansi')->nullable();
      $table->string('fb_account')->nullable();
      $table->string('ig_account')->nullable();
      $table->string('tiktok_account')->nullable();

      $table->text('desc')->nullable();
      
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
    Schema::dropIfExists('formulir_pendaftaran_c');
  }
}
