<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKonfirmasiPembayaranTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   
        Schema::defaultStringLength(191);
        Schema::create('konfirmasi_pembayaran', function (Blueprint $table) {
            $table->uuid('transaksi_id')->primary();    
            $table->uuid('user_id');                           
            $table->bigInteger('no_transaksi')->unique();  
            $table->tinyInteger('id_channel');
            $table->decimal('total_bayar',15,2)->default(0);
            $table->string('nomor_rekening_pengirim');
            $table->string('nama_rekening_pengirim');
            $table->string('nama_bank_pengirim');
            $table->text('desc')->nullable();
            $table->date('tanggal_bayar');
            $table->string('bukti_bayar')->default('storage/images/users/no_photo.png');  
            $table->boolean('verified')->default(false);          
            $table->timestamps();      

            $table->index('no_transaksi');            
            $table->index('user_id');
            $table->index('transaksi_id');

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
        Schema::dropIfExists('konfirmasi_pembayaran');        
    }
}