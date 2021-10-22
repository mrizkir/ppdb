<?php

use Illuminate\Database\Seeder;

use Carbon\Carbon;
use Spatie\Permission\Models\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::statement('DELETE FROM roles');
        \DB::statement('ALTER TABLE roles AUTO_INCREMENT = 1;');
        \DB::table('roles')->insert([
            [
                'name'=>'superadmin',
                'guard_name'=>'api',
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now()
            ],        
            [
                'name'=>'psb',
                'guard_name'=>'api',
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now()
            ],   
            [
                'name'=>'keuangan',
                'guard_name'=>'api',
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now()
            ],            
            [
                'name'=>'siswabaru',
                'guard_name'=>'api',
                'created_at'=>Carbon::now(),
                'updated_at'=>Carbon::now()
            ], 
        ]);

        $role = Role::findByName('siswabaru');
        $records=[
            'DASHBOARD_SHOW',
            'SPSB-GROUP',            
            'SPSB-PSB-FORMULIR-PENDAFTARAN_BROWSE',
            'SPSB-PSB-FORMULIR-PENDAFTARAN_SHOW',
            'SPSB-PSB-FORMULIR-PENDAFTARAN_STORE',
            'SPSB-PSB-FORMULIR-PENDAFTARAN_UPDATE',  
            'SPSB-PSB-PERSYARATAN_SHOW',  
            'SPSB-PSB-PERSYARATAN_STORE',  
            'KEUANGAN-KONFIRMASI-PEMBAYARAN_BROWSE',           
            'KEUANGAN-KONFIRMASI-PEMBAYARAN_SHOW',           
            'KEUANGAN-KONFIRMASI-PEMBAYARAN_STORE',  
            'SPSB-PSB-JADWAL-UJIAN_BROWSE',
            'SPSB-PSB-UJIAN-ONLINE_SHOW',              
            'SPSB-PSB-UJIAN-ONLINE_STORE',              
            'SPSB-PSB-UJIAN-ONLINE_UPDATE',                      
        ];
        $role->syncPermissions($records);
        
        $role = Role::findByName('keuangan');
        $records=[
            'DASHBOARD_SHOW',
            'KEUANGAN-GROUP',  
            'KEUANGAN-RINGKASAN_BROWSE', 

            'KEUANGAN-KOMPONEN-BIAYA_BROWSE',                          
            
            'KEUANGAN-BIAYA-KOMPONEN-PERIODE_BROWSE',           
            'KEUANGAN-BIAYA-KOMPONEN-PERIODE_STORE',           
            'KEUANGAN-BIAYA-KOMPONEN-PERIODE_SHOW',           
            'KEUANGAN-BIAYA-KOMPONEN-PERIODE_STORE',              
            'KEUANGAN-BIAYA-KOMPONEN-PERIODE_UPDATE',              
            'KEUANGAN-BIAYA-KOMPONEN-PERIODE_DESTROY',              
            
            'KEUANGAN-TRANSAKSI_BROWSE',           
            'KEUANGAN-TRANSAKSI_STORE',           
            'KEUANGAN-TRANSAKSI_SHOW',           
            'KEUANGAN-TRANSAKSI_STORE',              
            'KEUANGAN-TRANSAKSI_UPDATE',              
            'KEUANGAN-TRANSAKSI_DESTROY',              

            'KEUANGAN-KONFIRMASI-PEMBAYARAN_BROWSE',           
            'KEUANGAN-KONFIRMASI-PEMBAYARAN_STORE',           
            'KEUANGAN-KONFIRMASI-PEMBAYARAN_SHOW',           
            'KEUANGAN-KONFIRMASI-PEMBAYARAN_STORE',              
            'KEUANGAN-KONFIRMASI-PEMBAYARAN_UPDATE',              
            'KEUANGAN-KONFIRMASI-PEMBAYARAN_DESTROY',

            'AKADEMIK-GROUP',              
            'AKADEMIK-KEMAHASISWAAN-DAFTAR-MAHASISWA_BROWSE',              
        ];
        $role->syncPermissions($records);
    }
}
