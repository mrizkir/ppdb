<?php

use Illuminate\Database\Seeder;

use Carbon\Carbon;
use App\Models\System\ConfigurationModel;
class ConfigurationTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::statement('DELETE FROM configuration');
        
        \DB::table('configuration')->insert([
            'config_id'=>"101",
            'config_group'=>'identitas',
            'config_key'=>'NAMA_SEKOLAH',
            'config_value'=>'NAMA SEKOLAH',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);        

        \DB::table('configuration')->insert([
            'config_id'=>"102",
            'config_group'=>'identitas',
            'config_key'=>'NAMA_SEKOLAH_ALIAS',
            'config_value'=>'NAMA SEKOLAH ALIAS',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);

        \DB::table('configuration')->insert([
            'config_id'=>"103",
            'config_group'=>'identitas',
            'config_key'=>'BENTUK_SEKOLAH',
            'config_value'=>'smp',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);

        \DB::table('configuration')->insert([
            'config_id'=>"104",
            'config_group'=>'identitas',
            'config_key'=>'KODE_SEKOLAH',
            'config_value'=>'0',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);

        \DB::table('configuration')->insert([
            'config_id'=>"201",
            'config_group'=>'basic',
            'config_key'=>'DEFAULT_TA',
            'config_value'=>date('Y'),
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);
        
        \DB::table('configuration')->insert([
            'config_id'=>"202",
            'config_group'=>'basic',
            'config_key'=>'DEFAULT_SEMESTER',
            'config_value'=>1,
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);

        \DB::table('configuration')->insert([
            'config_id'=>"203",
            'config_group'=>'basic',
            'config_key'=>'DEFAULT_TAHUN_PENDAFTARAN',
            'config_value'=>date('Y'),
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);

        \DB::table('configuration')->insert([
            'config_id'=>"204",
            'config_group'=>'basic',
            'config_key'=>'DEFAULT_SEMESTER_PENDAFTARAN',
            'config_value'=>1,
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);
        
        \DB::table('configuration')->insert([
            'config_id'=>"205",
            'config_group'=>'basic',
            'config_key'=>'DEFAULT_KODE_JENJANG',
            'config_value'=>1,
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);
        
        \DB::table('configuration')->insert([
            'config_id'=>"206",
            'config_group'=>'basic',
            'config_key'=>'DEFAULT_BUKA_PPDB',
            'config_value'=>0,
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);
        
        \DB::table('configuration')->insert([
            'config_id'=>"701",
            'config_group'=>'report',
            'config_key'=>'HEADER_1',
            'config_value'=>'',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);
        
        \DB::table('configuration')->insert([
            'config_id'=>"702",
            'config_group'=>'report',
            'config_key'=>'HEADER_2',
            'config_value'=>'',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);

        \DB::table('configuration')->insert([
            'config_id'=>"703",
            'config_group'=>'report',
            'config_key'=>'HEADER_3',
            'config_value'=>'',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);
        
        \DB::table('configuration')->insert([
            'config_id'=>"704",
            'config_group'=>'report',
            'config_key'=>'HEADER_4',
            'config_value'=>'',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);
        
        \DB::table('configuration')->insert([
            'config_id'=>"705",
            'config_group'=>'report',
            'config_key'=>'HEADER_ADDRESS',
            'config_value'=>'',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);
        
        // theme
        \DB::table('configuration')->insert([
            'config_id'=>"801",
            'config_group'=>'theme',
            'config_key'=>'V-SYSTEM-BAR-CSS-CLASS',
            'config_value'=>'green lighten-2 white--text',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);

        \DB::table('configuration')->insert([
            'config_id'=>"802",
            'config_group'=>'theme',
            'config_key'=>'V-APP-BAR-NAV-ICON-CSS-CLASS',
            'config_value'=>'grey--text',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);

        \DB::table('configuration')->insert([
            'config_id'=>"803",
            'config_group'=>'theme',
            'config_key'=>'V-NAVIGATION-DRAWER-CSS-CLASS',
            'config_value'=>'green darken-1',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);
        
        \DB::table('configuration')->insert([
            'config_id'=>"804",
            'config_group'=>'theme',
            'config_key'=>'V-LIST-ITEM-BOARD-CSS-CLASS',
            'config_value'=>'yellow',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);

        \DB::table('configuration')->insert([
            'config_id'=>"805",
            'config_group'=>'theme',
            'config_key'=>'V-LIST-ITEM-BOARD-COLOR',
            'config_value'=>'green',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);
        
        \DB::table('configuration')->insert([
            'config_id'=>"806",
            'config_group'=>'theme',
            'config_key'=>'V-LIST-ITEM-ACTIVE-CSS-CLASS',
            'config_value'=>'green darken-1',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);

        //server
        \DB::table('configuration')->insert([
            'config_id'=>"910",
            'config_group'=>'server',
            'config_key'=>'EMAIL_SISWA_ISVALID',
            'config_value'=>'0',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);

        \DB::table('configuration')->insert([
            'config_id'=>"901",
            'config_group'=>'server',
            'config_key'=>'CAPTCHA_SITE_KEY',
            'config_value'=>'6LeSUNkZAAAAAAwcGMIdxVGpCp1ejqcQuofxLpju',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);

        \DB::table('configuration')->insert([
            'config_id'=>"902",
            'config_group'=>'server',
            'config_key'=>'CAPTCHA_PRIVATE_KEY',
            'config_value'=>'6LeSUNkZAAAAAP6sqAJmFeyrbSst0f1wMDCajmql',
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);
        
        ConfigurationModel::toCache();
    }
}
