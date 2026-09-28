<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FormulirNominalTransfer extends Migration
{
  /**
   * Nominal transfer milik paket formulir (user + tahun + jenjang).
   * Angka undangan yang sudah sah disalin. users.code hanya disalin
   * bila masih masuk rentang biaya tahun formulir itu.
   */
  public function up()
  {
    if (!Schema::hasTable('formulir_pendaftaran_a')) {
      return;
    }

    if (!Schema::hasColumn('formulir_pendaftaran_a', 'nominal_transfer')) {
      Schema::table('formulir_pendaftaran_a', function (Blueprint $table) {
        $table->unsignedInteger('nominal_transfer')->nullable()->after('ta');
      });
    }

    if (Schema::hasTable('undangan_pmb') && Schema::hasColumn('undangan_pmb', 'nominal_transfer')) {
      \DB::statement('
        UPDATE formulir_pendaftaran_a f
        JOIN undangan_pmb u ON u.formulir_id = f.id
        SET f.nominal_transfer = u.nominal_transfer
        WHERE f.nominal_transfer IS NULL
          AND u.nominal_transfer IS NOT NULL
          AND u.nominal_transfer > 0
      ');
    }

    if (Schema::hasTable('pe3_kombi_periode')) {
      \DB::statement('
        UPDATE formulir_pendaftaran_a f
        JOIN users us ON us.id = f.user_id
        JOIN pe3_kombi_periode k
          ON k.kombi_id = 101
         AND k.kode_jenjang = f.kode_jenjang
         AND k.tahun = f.ta
         AND k.biaya > 0
        SET f.nominal_transfer = us.code
        WHERE f.nominal_transfer IS NULL
          AND us.code >= k.biaya
          AND us.code <= k.biaya + 999
      ');
    }
  }

  public function down()
  {
    if (Schema::hasTable('formulir_pendaftaran_a') && Schema::hasColumn('formulir_pendaftaran_a', 'nominal_transfer')) {
      Schema::table('formulir_pendaftaran_a', function (Blueprint $table) {
        $table->dropColumn('nominal_transfer');
      });
    }
  }
}
