<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SdKategoriDisabilitas extends Migration
{
  /**
   * Kategori disabilitas boleh lebih dari satu, plus berkas pemeriksaan ahli.
   */
  public function up()
  {
    if (Schema::hasTable('persyaratan_ppdb') && !Schema::hasColumn('persyaratan_ppdb', 'file_pemeriksaan_ahli')) {
      Schema::table('persyaratan_ppdb', function (Blueprint $table) {
        $table->string('file_pemeriksaan_ahli')->nullable()->after('file_kia');
      });
    }

    if (!Schema::hasTable('formulir_kategori_disabilitas')) {
      \DB::statement("CREATE TABLE formulir_kategori_disabilitas (
        formulir_id CHAR(36) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
        id_kebutuhan TINYINT(4) NOT NULL,
        PRIMARY KEY (formulir_id, id_kebutuhan),
        CONSTRAINT formulir_kategori_disabilitas_formulir_id_foreign
          FOREIGN KEY (formulir_id) REFERENCES formulir_pendaftaran_a (id)
          ON DELETE CASCADE ON UPDATE CASCADE,
        CONSTRAINT formulir_kategori_disabilitas_id_kebutuhan_foreign
          FOREIGN KEY (id_kebutuhan) REFERENCES kebutuhan_khusus (id_kebutuhan)
          ON DELETE CASCADE ON UPDATE CASCADE
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    }
  }

  public function down()
  {
    Schema::dropIfExists('formulir_kategori_disabilitas');

    if (Schema::hasTable('persyaratan_ppdb') && Schema::hasColumn('persyaratan_ppdb', 'file_pemeriksaan_ahli')) {
      Schema::table('persyaratan_ppdb', function (Blueprint $table) {
        $table->dropColumn('file_pemeriksaan_ahli');
      });
    }
  }
}
