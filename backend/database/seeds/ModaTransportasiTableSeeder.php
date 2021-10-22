<?php

use Illuminate\Database\Seeder;

class ModaTransportasiTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::statement('DELETE FROM moda_transportasi');
        
        \DB::table('moda_transportasi')->insert([
            'id_moda'=>1,
            'nama_moda'=>'JALAN KAKI',
        ]);
        \DB::table('moda_transportasi')->insert([
            'id_moda'=>2,
            'nama_moda'=>'MOBIL PRIBADI',
        ]);
        \DB::table('moda_transportasi')->insert([
            'id_moda'=>3,
            'nama_moda'=>'SEPEDA MOTOR PRIBADI',
        ]);
        \DB::table('moda_transportasi')->insert([
            'id_moda'=>4,
            'nama_moda'=>'OJEK',
        ]);
        \DB::table('moda_transportasi')->insert([
            'id_moda'=>5,
            'nama_moda'=>'KENDARAAN UMUM/ANGKOT',
        ]);
        \DB::table('moda_transportasi')->insert([
            'id_moda'=>6,
            'nama_moda'=>'JEMPUTAN SEKOLAH',
        ]);
        \DB::table('moda_transportasi')->insert([
            'id_moda'=>7,
            'nama_moda'=>'LAINNYA',
        ]);        
    }
}
