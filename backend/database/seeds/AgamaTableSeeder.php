<?php

use Illuminate\Database\Seeder;

use Carbon\Carbon;

class AgamaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::statement('DELETE FROM agama');
        
        \DB::table('agama')->insert([
            'idagama'=>1,
            'nama_agama'=>'ISLAM',                        
        ]);        
        \DB::table('agama')->insert([
            'idagama'=>2,
            'nama_agama'=>'PROTESTAN',                        
        ]);        
        \DB::table('agama')->insert([
            'idagama'=>3,
            'nama_agama'=>'KATOLIK',                        
        ]);        
        \DB::table('agama')->insert([
            'idagama'=>4,
            'nama_agama'=>'HINDU',                        
        ]);        
        \DB::table('agama')->insert([
            'idagama'=>5,
            'nama_agama'=>'BUDDHA',                        
        ]);        
        \DB::table('agama')->insert([
            'idagama'=>6,
            'nama_agama'=>'KONGHUCU',                        
        ]);        

    }
}
