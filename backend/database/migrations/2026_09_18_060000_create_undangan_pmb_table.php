<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUndanganPmbTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::defaultStringLength(191);
    Schema::create('undangan_pmb', function (Blueprint $table) {
      $table->uuid('id')->primary();
      $table->uuid('user_id')->unique();
      $table->unsignedInteger('otp')->unique();
      $table->date('berlaku_mulai');
      $table->date('berlaku_sampai');
      $table->year('ta'); // tahun ajaran
      $table->tinyInteger('kode_jenjang');   // kode jenjang studi
      $table->boolean('used')->default(false);
      $table->uuid('created_by')->nullable();
      $table->timestamps();

      $table->index('otp');
      $table->index('used');
      $table->index('ta');
      $table->index('kode_jenjang');

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
    Schema::dropIfExists('undangan_pmb');
  }
}
