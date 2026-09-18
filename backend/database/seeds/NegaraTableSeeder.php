<?php

use Illuminate\Database\Seeder;

class NegaraTableSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    \DB::statement('DELETE FROM negara');

    $path = base_path('../negara.sql');
    if (!is_file($path)) {
      $path = database_path('seeds/negara.sql');
    }
    if (!is_file($path)) {
      throw new Exception('File negara.sql tidak ditemukan.');
    }

    $sql = file_get_contents($path);
    $sql = preg_replace('/CREATE TABLE `negara` \(.*?\) ENGINE=MyISAM;/s', '', $sql);
    $sql = preg_replace('/^--.*$/m', '', $sql);
    $sql = trim($sql);

    if ($sql === '') {
      throw new Exception('Isi negara.sql kosong.');
    }

    \DB::unprepared($sql);
  }
}
