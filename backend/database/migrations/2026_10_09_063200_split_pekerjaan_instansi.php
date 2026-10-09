<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SplitPekerjaanInstansi extends Migration
{
  /**
   * Pisahkan pekerjaan dan instansi ayah, ibu, dan wali.
   * Kolom lama pekerjaan_instansi tetap ada untuk isian yang belum dipecah.
   */
  public function up()
  {
    foreach (['formulir_pendaftaran_c', 'formulir_pendaftaran_d', 'formulir_pendaftaran_f'] as $tabel) {
      if (!Schema::hasTable($tabel)) {
        continue;
      }
      if (!Schema::hasColumn($tabel, 'pekerjaan')) {
        Schema::table($tabel, function (Blueprint $table) {
          $table->string('pekerjaan')->nullable()->after('pekerjaan_instansi');
        });
      }
      if (!Schema::hasColumn($tabel, 'instansi')) {
        Schema::table($tabel, function (Blueprint $table) {
          $table->string('instansi')->nullable()->after('pekerjaan');
        });
      }
    }
  }

  public function down()
  {
    foreach (['formulir_pendaftaran_c', 'formulir_pendaftaran_d', 'formulir_pendaftaran_f'] as $tabel) {
      if (!Schema::hasTable($tabel)) {
        continue;
      }
      if (Schema::hasColumn($tabel, 'instansi')) {
        Schema::table($tabel, function (Blueprint $table) {
          $table->dropColumn('instansi');
        });
      }
      if (Schema::hasColumn($tabel, 'pekerjaan')) {
        Schema::table($tabel, function (Blueprint $table) {
          $table->dropColumn('pekerjaan');
        });
      }
    }
  }
}
