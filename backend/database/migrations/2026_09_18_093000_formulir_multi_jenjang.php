<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class FormulirMultiJenjang extends Migration
{
  /**
   * Satu user bisa punya beberapa paket formulir (TK/SD/SMP/SMA).
   */
  public function up()
  {
    if (Schema::hasTable('formulir_pendaftaran_a') && !Schema::hasColumn('formulir_pendaftaran_a', 'id')) {
      Schema::table('formulir_pendaftaran_a', function (Blueprint $table) {
        $table->dropForeign(['user_id']);
      });

      \DB::statement('ALTER TABLE formulir_pendaftaran_a DROP PRIMARY KEY');
      Schema::table('formulir_pendaftaran_a', function (Blueprint $table) {
        $table->uuid('id')->nullable()->after('user_id');
      });
      \DB::statement('UPDATE formulir_pendaftaran_a SET id = user_id WHERE id IS NULL');
      \DB::statement('ALTER TABLE formulir_pendaftaran_a MODIFY id CHAR(36) NOT NULL');
      Schema::table('formulir_pendaftaran_a', function (Blueprint $table) {
        $table->primary('id');
        $table->index('user_id');
        $table->unique(['user_id', 'ta', 'kode_jenjang'], 'formulir_a_user_ta_jenjang_unique');
        $table->foreign('user_id')
          ->references('id')
          ->on('users')
          ->onDelete('cascade')
          ->onUpdate('cascade');
      });
    }

    $childTables = [
      'formulir_pendaftaran_b',
      'formulir_pendaftaran_c',
      'formulir_pendaftaran_d',
      'formulir_pendaftaran_e',
      'formulir_pendaftaran_f',
      'persyaratan_ppdb',
    ];
    foreach ($childTables as $tableName) {
      if (!Schema::hasTable($tableName) || Schema::hasColumn($tableName, 'formulir_id') || !Schema::hasColumn($tableName, 'user_id')) {
        continue;
      }
      Schema::table($tableName, function (Blueprint $table) {
        $table->dropForeign(['user_id']);
      });
      \DB::statement("ALTER TABLE {$tableName} DROP PRIMARY KEY");
      \DB::statement("ALTER TABLE {$tableName} CHANGE user_id formulir_id CHAR(36) NOT NULL");
      Schema::table($tableName, function (Blueprint $table) {
        $table->primary('formulir_id');
        $table->foreign('formulir_id')
          ->references('id')
          ->on('formulir_pendaftaran_a')
          ->onDelete('cascade')
          ->onUpdate('cascade');
      });
    }

    if (Schema::hasTable('undangan_pmb')) {
      if (!Schema::hasColumn('undangan_pmb', 'formulir_id')) {
        Schema::table('undangan_pmb', function (Blueprint $table) {
          $table->uuid('formulir_id')->nullable()->after('user_id');
        });
      }

      \DB::statement('UPDATE undangan_pmb u
        JOIN formulir_pendaftaran_a a ON a.user_id = u.user_id AND a.ta = u.ta AND a.kode_jenjang = u.kode_jenjang
        SET u.formulir_id = a.id
        WHERE u.formulir_id IS NULL');
      \DB::statement('UPDATE undangan_pmb SET formulir_id = user_id WHERE formulir_id IS NULL');

      $indexes = collect(\DB::select('SHOW INDEX FROM undangan_pmb'))->pluck('Key_name')->unique();
      if ($indexes->contains('undangan_pmb_user_id_unique')) {
        Schema::table('undangan_pmb', function (Blueprint $table) {
          $table->dropForeign(['user_id']);
        });
        Schema::table('undangan_pmb', function (Blueprint $table) {
          $table->dropUnique(['user_id']);
          $table->index('user_id');
          $table->foreign('user_id')
            ->references('id')
            ->on('users')
            ->onDelete('cascade')
            ->onUpdate('cascade');
        });
      }

      \DB::statement('ALTER TABLE undangan_pmb MODIFY formulir_id CHAR(36) NOT NULL');

      $indexes = collect(\DB::select('SHOW INDEX FROM undangan_pmb'))->pluck('Key_name')->unique();
      Schema::table('undangan_pmb', function (Blueprint $table) use ($indexes) {
        if (!$indexes->contains('undangan_pmb_formulir_id_unique')) {
          $table->unique('formulir_id');
        }
        $table->foreign('formulir_id')
          ->references('id')
          ->on('formulir_pendaftaran_a')
          ->onDelete('cascade')
          ->onUpdate('cascade');
      });

      $undangans = \DB::table('undangan_pmb')->get();
      foreach ($undangans as $undangan) {
        $source = \App\Models\SPSB\FormulirPendaftaranAModel::find($undangan->formulir_id);
        if (is_null($source)) {
          continue;
        }
        if ((int) $source->ta === (int) $undangan->ta && (int) $source->kode_jenjang === (int) $undangan->kode_jenjang) {
          continue;
        }
        $clone = \App\Helpers\HelperFormulir::cloneToJenjang($source, $undangan->ta, $undangan->kode_jenjang);
        \DB::table('undangan_pmb')->where('id', $undangan->id)->update([
          'formulir_id' => $clone->id,
        ]);
      }
    }
  }

  public function down()
  {
    if (Schema::hasTable('undangan_pmb') && Schema::hasColumn('undangan_pmb', 'formulir_id')) {
      Schema::table('undangan_pmb', function (Blueprint $table) {
        $table->dropForeign(['formulir_id']);
        $table->dropUnique(['formulir_id']);
        $table->dropColumn('formulir_id');
        $table->unique('user_id');
      });
    }

    $childTables = [
      'formulir_pendaftaran_b',
      'formulir_pendaftaran_c',
      'formulir_pendaftaran_d',
      'formulir_pendaftaran_e',
      'formulir_pendaftaran_f',
      'persyaratan_ppdb',
    ];
    foreach ($childTables as $tableName) {
      if (!Schema::hasTable($tableName) || !Schema::hasColumn($tableName, 'formulir_id')) {
        continue;
      }
      Schema::table($tableName, function (Blueprint $table) {
        $table->dropForeign(['formulir_id']);
      });
      \DB::statement("ALTER TABLE {$tableName} DROP PRIMARY KEY");
      \DB::statement("ALTER TABLE {$tableName} CHANGE formulir_id user_id CHAR(36) NOT NULL");
      Schema::table($tableName, function (Blueprint $table) {
        $table->primary('user_id');
        $table->foreign('user_id')
          ->references('id')
          ->on('users')
          ->onDelete('cascade')
          ->onUpdate('cascade');
      });
    }

    if (Schema::hasColumn('formulir_pendaftaran_a', 'id')) {
      Schema::table('formulir_pendaftaran_a', function (Blueprint $table) {
        $table->dropForeign(['user_id']);
        $table->dropUnique('formulir_a_user_ta_jenjang_unique');
      });
      \DB::statement('ALTER TABLE formulir_pendaftaran_a DROP PRIMARY KEY');
      Schema::table('formulir_pendaftaran_a', function (Blueprint $table) {
        $table->dropColumn('id');
        $table->primary('user_id');
        $table->foreign('user_id')
          ->references('id')
          ->on('users')
          ->onDelete('cascade')
          ->onUpdate('cascade');
      });
    }
  }
}
