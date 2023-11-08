<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class KomponenBiayaTableSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    \DB::statement('DELETE FROM pe3_kombi');

    \DB::table('pe3_kombi')->insert([
      'id'=>"101",
      'nama'=>'BIAYA PENDAFTARAN + BIAYA FORMULIR',
      'periode'=>'sekali',
      'created_at'=>Carbon::now(),
      'updated_at'=>Carbon::now()
    ]);
  }
}
