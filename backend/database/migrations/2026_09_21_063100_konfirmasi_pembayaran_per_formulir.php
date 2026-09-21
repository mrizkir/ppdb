<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class KonfirmasiPembayaranPerFormulir extends Migration
{
  public function up()
  {
    if (!Schema::hasTable('konfirmasi_pembayaran')) {
      return;
    }

    Schema::table('konfirmasi_pembayaran', function (Blueprint $table) {
      if (!Schema::hasColumn('konfirmasi_pembayaran', 'formulir_id')) {
        $table->uuid('formulir_id')->nullable()->after('user_id');
      }
      if (!Schema::hasColumn('konfirmasi_pembayaran', 'ta')) {
        $table->year('ta')->nullable()->after('formulir_id');
      }
      if (!Schema::hasColumn('konfirmasi_pembayaran', 'kode_jenjang')) {
        $table->tinyInteger('kode_jenjang')->nullable()->after('ta');
      }
    });

    $indexes = collect(\DB::select('SHOW INDEX FROM konfirmasi_pembayaran'));
    $uniqueNoTransaksi = $indexes->first(function ($idx) {
      return (int) $idx->Non_unique === 0 && $idx->Column_name === 'no_transaksi';
    });
    if ($uniqueNoTransaksi) {
      try {
        Schema::table('konfirmasi_pembayaran', function (Blueprint $table) use ($uniqueNoTransaksi) {
          $table->dropUnique($uniqueNoTransaksi->Key_name);
        });
      } catch (\Exception $e) {
      }
    }

    $indexes = collect(\DB::select('SHOW INDEX FROM konfirmasi_pembayaran'))->pluck('Key_name')->unique();
    Schema::table('konfirmasi_pembayaran', function (Blueprint $table) use ($indexes) {
      if (!$indexes->contains('konfirmasi_pembayaran_formulir_id_index')) {
        $table->index('formulir_id');
      }
      if (!$indexes->contains('konfirmasi_pembayaran_user_formulir_index')) {
        $table->index(['user_id', 'formulir_id'], 'konfirmasi_pembayaran_user_formulir_index');
      }
    });

    if (Schema::hasTable('formulir_pendaftaran_a')) {
      try {
        Schema::table('konfirmasi_pembayaran', function (Blueprint $table) {
          $table->foreign('formulir_id')
            ->references('id')
            ->on('formulir_pendaftaran_a')
            ->onDelete('set null')
            ->onUpdate('cascade');
        });
      } catch (\Exception $e) {
      }
    }
  }

  public function down()
  {
    if (!Schema::hasTable('konfirmasi_pembayaran')) {
      return;
    }

    Schema::table('konfirmasi_pembayaran', function (Blueprint $table) {
      try {
        $table->dropForeign(['formulir_id']);
      } catch (\Exception $e) {
      }
      if (Schema::hasColumn('konfirmasi_pembayaran', 'formulir_id')) {
        $table->dropColumn(['formulir_id', 'ta', 'kode_jenjang']);
      }
    });
  }
}
