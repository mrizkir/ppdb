<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormulirPendaftaranATable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::defaultStringLength(191);

    Schema::create('formulir_pendaftaran_a', function (Blueprint $table) {
      $table->uuid('user_id')->primary();
      $table->string('nama_siswa')->nullable();
      $table->string('nisn')->nullable();
      $table->string('nama_panggilan')->nullable();
      $table->enum('jk',['L','P'])->default('L');
      $table->string('nik',60)->nullable();
      $table->string('tempat_lahir')->nullable();
      $table->date('tanggal_lahir')->nullable();
      $table->tinyInteger('idagama')->nullable();
      $table->tinyInteger('id_kebutuhan_khusus')->nullable();

      $table->string('address1_desa_id')->nullable();
      $table->string('address1_kelurahan')->nullable();
      $table->string('address1_kecamatan_id')->nullable();
      $table->string('address1_kecamatan')->nullable();
      $table->string('address1_kabupaten_id')->nullable();
      $table->string('address1_kabupaten')->nullable();
      $table->string('address1_provinsi_id')->nullable();
      $table->string('address1_provinsi')->nullable();
      $table->string('alamat_tempat_tinggal')->nullable();
      $table->string('address1_rt',4)->nullable();
      $table->string('address1_rw',4)->nullable();
      $table->string('kode_pos',5)->nullable();
      $table->integer('kewarganegaraan')->nullable();

      $table->string('asal_sekolah')->nullable();
      $table->tinyInteger('anak_ke')->nullable();
      $table->tinyInteger('jumlah_saudara')->nullable();
      $table->enum('golongan_darah',['-','A','B','AB','O'])->nullable();
      $table->string('penyakit')->nullable();
      $table->string('avoid_food')->nullable();
      $table->integer('tinggi')->nullable();
      $table->integer('berat_badan')->nullable();
      $table->enum('ukuran_seragam',['S','M','L','XL','9','10','11','12','13'])->default('S');
      $table->tinyInteger('id_moda')->nullable();
      $table->string('jarak_ke_sekolah')->nullable();
      $table->string('waktu_tempuh')->nullable();

      $table->string('sibling_tk')->nullable();
      $table->string('sibling_sd')->nullable();
      $table->string('sibling_smp')->nullable();
      $table->string('sibling_sma')->nullable();

      $table->tinyInteger('kode_jenjang');
      $table->year('ta');
      
      $table->text('desc')->nullable();

      $table->timestamps();

      $table->index('nik');
      $table->index('nisn');
      $table->index('kode_jenjang');
      $table->index('ta');

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
    Schema::dropIfExists('formulir_pendaftaran_a');
  }
}
