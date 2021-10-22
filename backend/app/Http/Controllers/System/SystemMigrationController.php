<?php

namespace App\Http\Controllers\System;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Ramsey\Uuid\Uuid;
use Illuminate\Validation\Rule;
use App\Models\SPSB\FormulirPendaftaranAModel;
use App\Models\Akademik\RegisterSiswaModel;
use App\Models\Akademik\DulangModel;
use App\Models\System\DataSiswaMigrationModel;
use App\Models\User;
use Spatie\Permission\Models\Role;

class SystemMigrationController extends Controller {    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   
        $this->hasPermissionTo('SYSTEM-MIGRATION_BROWSE');

        $this->validate($request, [           
            'TA'=>'required',            
        ]);
        
        $ta=$request->input('TA');
        $daftar_tasmt=[];
        
        for ($tahun=$ta;$tahun < 2020; $tahun++)
        {
            $daftar_tasmt[]=[
                'id'=>$tahun.'1',
                'ta'=>$tahun.'/'.($tahun+1),
                'semester'=>'GANJIL',
            ];   
            $daftar_tasmt[]=[
                'id'=>$tahun.'2',
                'ta'=>$tahun.'/'.($tahun+1),
                'semester'=>'GENAP',
            ];   
        }        
        return Response()->json([
                                'status'=>1,
                                'pid'=>'fetchdata',
                                'daftar_tasmt'=>$daftar_tasmt,
                                'message'=>'Fetch data daftar tahun semester berhasil diperoleh'
                            ],200); 
    }
    public function store(Request $request)
    {
        $this->hasPermissionTo('SYSTEM-MIGRATION_STORE');
        
        $status_siswa=json_decode($request->input('status_siswa'),true);
        $request->merge(['status_siswa'=>$status_siswa]);

        $this->validate($request, [
            'nis'=>'required|numeric|unique:register_siswa,nis',        
            'nirm'=>'required|numeric|unique:register_siswa,nirm',
            'nama_siswa'=>'required',            
            'dosen_id'=>[
                'required',
                Rule::exists('dosen','user_id')->where(function($query){
                    return $query->where('is_dw',1);
                })
            ],            
            'kode_jenjang'=>'required|numeric',            
            'idkelas'=>'required',
            'tahun_pendaftaran'=>'required',
            'status_siswa.*'=>'required',                        
        ]);
        
        $user = \DB::transaction(function () use ($request){
            $now = \Carbon\Carbon::now()->toDateTimeString();   
            $uuid=Uuid::uuid4()->toString();
            $no_hp=mt_rand(1000,9999);
            $ta=$request->input('tahun_pendaftaran');
            $nis=$request->input('nis');
            $user=User::create([
                'id'=>$uuid,
                'name'=>$request->input('nama_siswa'),
                'email'=>"$uuid@staimutanjungpinang.ac.id",
                'username'=> $nis,
                'password'=>Hash::make('12345678'),
                'nomor_hp'=>"+62$no_hp",
                'ta'=>$ta,
                'email_verified_at'=>'',
                'theme'=>'default',  
                'code'=>0,     
                'default_role'=>'siswa',     
                'active'=>1,         
                'foto'=>'storage/images/users/no_photo.png', 
                'created_at'=>$now, 
                'updated_at'=>$now
            ]);    
            DataSiswaMigrationModel::create([
                'id'=>Uuid::uuid4()->toString(),
                'user_id'=>$user->id,
                'nis'=>$nis,
                'nama_siswa'=>$request->input('nama_siswa'),  
                'aktivitas'=>"Input data ke user berhasil dengan username ($nis)",  
                'tahun'=>$ta,
                'idsmt'=>1
            ]);
            $no_formulir='1'.mt_rand();
            $formulir=FormulirPendaftaranAModel::create([
                'user_id'=>$user->id,
                'no_formulir'=>$no_formulir,
                'nama_siswa'=>$request->input('nama_siswa'),                
                'telp_hp'=>$request->input('nomor_hp'),
                'kode_jenjang'=>$request->input('kode_jenjang'),
                'ta'=>$ta,
                'idsmt'=>1,
                'idkelas'=>$request->input('idkelas'),
            ]);
            DataSiswaMigrationModel::create([
                'id'=>Uuid::uuid4()->toString(),
                'user_id'=>$user->id,
                'nis'=>$nis,
                'nama_siswa'=>$request->input('nama_siswa'),  
                'aktivitas'=>"Input data ke formulir pendaftaran berhasil dengan nomor formulir ($no_formulir)",  
                'tahun'=>$ta,
                'idsmt'=>1
            ]);
            $siswa=RegisterSiswaModel::create([
                'user_id'=>$user->id,
                'dosen_id'=>$request->input('dosen_id'),
                'konsentrasi_id'=>null,
                'nis'=>$nis,
                'nirm'=>$request->input('nirm'),
                'tahun'=>$formulir->ta,
                'idsmt'=>$formulir->idsmt,
                'kjur'=>$formulir->kode_jenjang,
                'k_status'=>'A',
                'idkelas'=>$formulir->idkelas,
            ]);
            
            $role='siswa';   
            $user->assignRole($role);
            $permission=Role::findByName('siswa')->permissions;
            $user->givePermissionTo($permission->pluck('name'));            
            
            DataSiswaMigrationModel::create([
                'id'=>Uuid::uuid4()->toString(),
                'user_id'=>$user->id,
                'nis'=>$nis,
                'nama_siswa'=>$request->input('nama_siswa'),  
                'aktivitas'=>"Input data ke register siswa berhasil.",  
                'tahun'=>$ta,
                'idsmt'=>1
            ]);
            $status_siswa=$request->input('status_siswa');            
            $i=0;
            for ($tahun=$ta;$tahun < 2020; $tahun++)
            {
                if (isset($status_siswa[$i]) && isset($status_siswa[$i+1]))
                {
                    $status_sebelumnya = $i > 0 ? $status_siswa[$i-1]:$status_siswa[$i];
                    DulangModel::create([
                        'id'=>Uuid::uuid4()->toString(),
                        'user_id'=>$user->id,
                        'nis'=>$siswa->nis,
                        'tahun'=>$tahun,
                        'idsmt'=>1,
                        'tasmt'=>$tahun.'1',
                        'idkelas'=>$formulir->idkelas,
                        'status_sebelumnya'=>$status_sebelumnya,
                        'k_status'=>$status_siswa[$i],
                    ]);
                    
                    DataSiswaMigrationModel::create([
                        'id'=>Uuid::uuid4()->toString(),
                        'user_id'=>$user->id,
                        'nis'=>$nis,
                        'nama_siswa'=>$request->input('nama_siswa'),  
                        'aktivitas'=>"Input data ke daftar ulang tahun ".$tahun."1 berhasil dengan status ".$status_siswa[$i],  
                        'tahun'=>$ta,
                        'idsmt'=>1
                    ]);
                    $i+=1;                    
                    DulangModel::create([
                        'id'=>Uuid::uuid4()->toString(),
                        'user_id'=>$user->id,
                        'nis'=>$siswa->nis,
                        'tahun'=>$tahun,
                        'idsmt'=>2,
                        'tasmt'=>$tahun.'2',
                        'idkelas'=>$formulir->idkelas,
                        'status_sebelumnya'=>$status_siswa[$i-1],
                        'k_status'=>$status_siswa[$i],
                    ]);
                    
                    DataSiswaMigrationModel::create([
                        'id'=>Uuid::uuid4()->toString(),
                        'user_id'=>$user->id,
                        'nis'=>$nis,
                        'nama_siswa'=>$request->input('nama_siswa'),  
                        'aktivitas'=>"Input data ke daftar ulang tahun ".$tahun."2 berhasil dengan status ".$status_siswa[$i],  
                        'tahun'=>$ta,
                        'idsmt'=>1
                    ]);
                    $i+=1;
                }                
            }   
            return $user;
        });        
        return Response()->json([
                                'status'=>1,
                                'pid'=>'store',                                
                                'user'=>$user,                                
                                'message'=>'Proses migrasi siswa ini berhasil dilakukan, silahkan cek dimasing-masing halaman'
                            ],200);
    }
}
