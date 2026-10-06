<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UndanganNomorSurat extends Migration
{
  /**
   * Nomor surat pemberitahuan, diisi panitia sebelum cetak.
   */
  public function up()
  {
    if (!Schema::hasTable('undangan_pmb')) {
      return;
    }

    if (!Schema::hasColumn('undangan_pmb', 'nomor_surat')) {
      Schema::table('undangan_pmb', function (Blueprint $table) {
        $table->string('nomor_surat', 50)->nullable()->after('kode_jenjang');
      });
    }
  }

  public function down()
  {
    if (Schema::hasTable('undangan_pmb') && Schema::hasColumn('undangan_pmb', 'nomor_surat')) {
      Schema::table('undangan_pmb', function (Blueprint $table) {
        $table->dropColumn('nomor_surat');
      });
    }
  }
}
