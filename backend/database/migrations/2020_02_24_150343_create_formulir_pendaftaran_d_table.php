<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormulirPendaftaranDTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::defaultStringLength(191);

        Schema::create('formulir_pendaftaran_d', function (Blueprint $table) {
            $table->uuid('user_id')->primary();
            $table->string('nama_ibu')->nullable();
            $table->enum('hubungan',['IBU_KANDUNG','IBU_SAMBUNG','IBU_WALI'])->default('IBU_KANDUNG');
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
            $table->decimal('penghasilan_bulanan',15,2)->default(0);

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
        Schema::dropIfExists('formulir_pendaftaran_d');
    }
}
