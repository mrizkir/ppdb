<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UndanganNominalTransfer extends Migration
{
  /**
   * Nominal transfer undangan: biaya tahun itu + kode unik 1-999.
   * Disimpan sekali supaya pengisian OTP ulang tidak mengubah angka.
   */
  public function up()
  {
    if (!Schema::hasTable('undangan_pmb')) {
      return;
    }

    if (!Schema::hasColumn('undangan_pmb', 'nominal_transfer')) {
      Schema::table('undangan_pmb', function (Blueprint $table) {
        $table->unsignedInteger('nominal_transfer')->nullable()->after('kode_jenjang');
      });
    }
  }

  public function down()
  {
    if (Schema::hasTable('undangan_pmb') && Schema::hasColumn('undangan_pmb', 'nominal_transfer')) {
      Schema::table('undangan_pmb', function (Blueprint $table) {
        $table->dropColumn('nominal_transfer');
      });
    }
  }
}
