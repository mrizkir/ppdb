<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKuotaPendaftaranTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {   
    Schema::defaultStringLength(191);
    Schema::create('kuota_pendaftaran', function (Blueprint $table) {
      $table->uuid('id')->primary();
      $table->year('tahun');            
      $table->tinyInteger('kode_jenjang');      
      $table->tinyInteger('kuota_l');      
      $table->tinyInteger('kuota_p');   
      
      $table->timestamps();
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('kuota_pendaftaran');
  }
}
