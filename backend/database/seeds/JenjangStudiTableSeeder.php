<?php

use Illuminate\Database\Seeder;

use App\Models\System\ConfigurationModel;
class JenjangStudiTableSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    \DB::statement('DELETE FROM jenjang_studi');
    
    \DB::table('jenjang_studi')->insert([
      'kode_jenjang'=>1,
      'nama_jenjang'=>'TAMAN KANAK-KANAN (TK)',
    ]);               
    
    \DB::table('jenjang_studi')->insert([
      'kode_jenjang'=>2,
      'nama_jenjang'=>'SEKOLAH DASAR (SD)',
    ]);               

    \DB::table('jenjang_studi')->insert([
      'kode_jenjang'=>3,
      'nama_jenjang'=>'SEKOLAH MENENGAH PERTAMA (SMP)',
    ]);  

    \DB::table('jenjang_studi')->insert([
      'kode_jenjang'=>4,
      'nama_jenjang'=>'SEKOLAH MENENGAH ATAS (SMA)',
    ]);           
  }
}
