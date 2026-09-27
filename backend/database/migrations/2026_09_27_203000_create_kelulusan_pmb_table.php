<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKelulusanPmbTable extends Migration
{
  /**
   * Keputusan panitia: lulus atau tidak lulus, per formulir jenjang.
   */
  public function up()
  {
    if (Schema::hasTable('kelulusan_pmb')) {
      return;
    }

    Schema::defaultStringLength(191);
    Schema::create('kelulusan_pmb', function (Blueprint $table) {
      $table->uuid('id')->primary();
      $table->uuid('formulir_id')->unique();
      $table->uuid('user_id');
      $table->year('ta');
      $table->tinyInteger('kode_jenjang');
      $table->tinyInteger('ket_lulus');
      $table->uuid('decided_by')->nullable();
      $table->timestamps();

      $table->index('user_id');
      $table->index('ta');
      $table->index('kode_jenjang');
      $table->index('ket_lulus');

      $table->foreign('formulir_id')
        ->references('id')
        ->on('formulir_pendaftaran_a')
        ->onDelete('cascade')
        ->onUpdate('cascade');

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
    Schema::dropIfExists('kelulusan_pmb');
  }
}
