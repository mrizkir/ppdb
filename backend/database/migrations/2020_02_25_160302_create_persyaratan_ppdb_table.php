<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePersyaratanPPDBTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::defaultStringLength(191);

        Schema::create('persyaratan_ppdb', function (Blueprint $table) {
            $table->uuid('user_id')->primary();            
            $table->string('file_fotoselfi')->nullable();            
            $table->string('file_ktp_ayah')->nullable();            
            $table->string('file_ktp_ibu')->nullable();            
            $table->string('file_kk')->nullable();            
            $table->string('file_aktalahir')->nullable();            
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
        Schema::dropIfExists('persyaratan_ppdb');
    }
}
