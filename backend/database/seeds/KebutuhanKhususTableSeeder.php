<?php

use Illuminate\Database\Seeder;

class KebutuhanKhususTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::statement('DELETE FROM kebutuhan_khusus');

        \DB::table('kebutuhan_khusus')->insert([
            'id_kebutuhan'=>1,
            'nama_kebutuhan'=>'TIDAK',
        ]);
        \DB::table('kebutuhan_khusus')->insert([
            'id_kebutuhan'=>2,
            'nama_kebutuhan'=>'NETRA',
        ]);
        \DB::table('kebutuhan_khusus')->insert([
            'id_kebutuhan'=>3,
            'nama_kebutuhan'=>'RUNGU',
        ]);
        \DB::table('kebutuhan_khusus')->insert([
            'id_kebutuhan'=>4,
            'nama_kebutuhan'=>'GRAHITA RINGAN',
        ]);
        \DB::table('kebutuhan_khusus')->insert([
            'id_kebutuhan'=>5,
            'nama_kebutuhan'=>'GRAHITA SEDANG',
        ]);
        \DB::table('kebutuhan_khusus')->insert([
            'id_kebutuhan'=>6,
            'nama_kebutuhan'=>'DAKSA RINGAN',
        ]);
        \DB::table('kebutuhan_khusus')->insert([
            'id_kebutuhan'=>7,
            'nama_kebutuhan'=>'DAKSA SEDANG',
        ]);
        \DB::table('kebutuhan_khusus')->insert([
            'id_kebutuhan'=>8,
            'nama_kebutuhan'=>'LARAS',
        ]);
        \DB::table('kebutuhan_khusus')->insert([
            'id_kebutuhan'=>9,
            'nama_kebutuhan'=>'WICARA',
        ]);
        \DB::table('kebutuhan_khusus')->insert([
            'id_kebutuhan'=>10,
            'nama_kebutuhan'=>'TUNA GANDA',
        ]);
        \DB::table('kebutuhan_khusus')->insert([
            'id_kebutuhan'=>11,
            'nama_kebutuhan'=>'CERDAS ISTIMEWA',
        ]);
        \DB::table('kebutuhan_khusus')->insert([
            'id_kebutuhan'=>12,
            'nama_kebutuhan'=>'HIPER AKTIF',
        ]);
        \DB::table('kebutuhan_khusus')->insert([
            'id_kebutuhan'=>13,
            'nama_kebutuhan'=>'BAKAT ISTIMEWA',
        ]);
        \DB::table('kebutuhan_khusus')->insert([
            'id_kebutuhan'=>14,
            'nama_kebutuhan'=>'DOWN SINDROM',
        ]);
        \DB::table('kebutuhan_khusus')->insert([
            'id_kebutuhan'=>15,
            'nama_kebutuhan'=>'INDIGO',
        ]);
        \DB::table('kebutuhan_khusus')->insert([
            'id_kebutuhan'=>16,
            'nama_kebutuhan'=>'AUTIS',
        ]);
    }
}
