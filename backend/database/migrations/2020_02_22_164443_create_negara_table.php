<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNegaraTable extends Migration
{
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::defaultStringLength(191);
    Schema::create('negara', function (Blueprint $table) {
      $table->increments('id');
      $table->string('country_code', 2);
      $table->string('country_name', 100);
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('negara');
  }
}
