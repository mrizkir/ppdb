<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFormulirPendaftaranBTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::defaultStringLength(191);

    Schema::create('formulir_pendaftaran_b', function (Blueprint $table) {
      $table->uuid('user_id')->primary();
      $table->string('tinggal_bersama')->nullable();
      $table->enum('status_pernikahan',['UTUH','PISAH','CERAI'])->default('UTUH');
      
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
    Schema::dropIfExists('formulir_pendaftaran_b');
  }
}
