<?php

namespace App\Http\Controllers\System;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserDosen;
use Spatie\Permission\Models\Role;
use Ramsey\Uuid\Uuid;
use Illuminate\Validation\Rule;

class UsersPSBController extends Controller {         
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {           
        $this->hasPermissionTo('SYSTEM-USERS-PSB_BROWSE');
        $data = User::where('default_role','psb')
                    ->orderBy('username','ASC')
                    ->get();       
                    
        $role = Role::findByName('psb');
        return Response()->json([
                                'status'=>1,
                                'pid'=>'fetchdata',
                                'role'=>$role,
                                'users'=>$data,
                                'message'=>'Fetch data users PSB berhasil diperoleh'
                            ], 200);  
    }    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->hasPermissionTo('SYSTEM-USERS-PSB_STORE');
        $this->validate($request, [
            'name'=>'required',
            'email'=>'required|string|email|unique:users',
            'nomor_hp'=>'required|string|unique:users',
            'username'=>'required|string|unique:users',
            'password'=>'required',            
            'kode_jenjang'=>'required',
        ]);
        
        $user = \DB::transaction(function () use ($request){
            $now = \Carbon\Carbon::now()->toDateTimeString();
            $user=User::create([
                'id'=>Uuid::uuid4()->toString(),
                'name'=>$request->input('name'),
                'email'=>$request->input('email'),
                'nomor_hp'=>$request->input('nomor_hp'),
                'username'=> $request->input('username'),
                'password'=>Hash::make($request->input('password')),                        
                'theme'=>'default',
                'default_role'=>'psb',            
                'foto'=> 'images/users/no_photo.png',
                'created_at'=>$now, 
                'updated_at'=>$now
            ]);
            $role='psb';   
            $user->assignRole($role);   
            
            $permission=Role::findByName('psb')->permissions;
            $permissions=$permission->pluck('name');
            $user->givePermissionTo($permissions);

            $user_id=$user->id;
            $daftar_jenjang=json_decode($request->input('kode_jenjang'),true);
            foreach($daftar_jenjang as $v)
            {
                $sql = "
                    INSERT INTO usersprodi (                    
                        user_id, 
                        kode_jenjang,
                        kode_prodi,
                        nama_prodi,
                        nama_prodi_alias,
                        kode_jenjang,
                        nama_jenjang,                                                        
                        created_at, 
                        updated_at
                    ) 
                    SELECT
                        '$user_id',                    
                        id,
                        kode_prodi,
                        nama_prodi,
                        nama_prodi_alias,
                        kode_jenjang,
                        nama_jenjang,                          
                        NOW() AS created_at,
                        NOW() AS updated_at
                    FROM prodi                    
                    WHERE 
                        id='$v' 
                ";

                \DB::statement($sql); 
            }

            $daftar_roles=json_decode($request->input('role_id'),true);
            foreach($daftar_roles as $v)
            {
                if ($v=='dosen' || $v=='dosenwali' )
                {
                    $user->assignRole($v);   
                    $permission=Role::findByName($v)->permissions;
                    $permissions=$permission->pluck('name');
                    $user->givePermissionTo($permissions);

                    if ($v=='dosen')
                    {
                        UserDosen::create([
                            'user_id'=>$user->id,
                            'nama_dosen'=>$request->input('name'),                                                            
                        ]);
                        if ($v=='dosenwali')
                        {
                            \DB::table('dosen')
                                ->where('user_id',$user->id)
                                ->update(['is_dw'=>true]);
                        }
                    }                    
                }
            }

            \App\Models\System\ActivityLog::log($request,[
                                            'object' => $this->guard()->user(), 
                                            'object_id' => $this->guard()->user()->id, 
                                            'user_id' => $this->getUserid(), 
                                            'message' => 'Menambah user PSB('.$user->username.') berhasil'
                                        ]);

            return $user;
        });

        return Response()->json([
                                    'status'=>1,
                                    'pid'=>'store',
                                    'user'=>$user,                                    
                                    'message'=>'Data user PSB berhasil disimpan.'
                                ], 200); 

    }
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->hasPermissionTo('SYSTEM-USERS-PSB_UPDATE');

        $user = User::find($id);
        if (is_null($user))
        {
            return Response()->json([
                                    'status'=>1,
                                    'pid'=>'update',                
                                    'message'=>["User ID ($id) gagal diupdate"]
                                ],422); 
        }
        else
        {
            $user = \DB::transaction(function () use ($request,$user){
                $this->validate($request, [
                                            'username'=>[
                                                            'required',
                                                            'unique:users,username,'.$user->id
                                                        ],           
                                            'name'=>'required',            
                                            'email'=>'required|string|email|unique:users,email,'.$user->id,
                                            'nomor_hp'=>'required|string|unique:users,nomor_hp,'.$user->id,   
                                            'kode_jenjang'=>'required',           
                                        ]); 
                                        
                $user->name = $request->input('name');
                $user->email = $request->input('email');
                $user->nomor_hp = $request->input('nomor_hp');
                $user->username = $request->input('username');
                if (!empty(trim($request->input('password')))) {
                    $user->password = Hash::make($request->input('password'));
                }    
                $user->updated_at = \Carbon\Carbon::now()->toDateTimeString();
                $user->save();

                $user_id=$user->id;
                \DB::table('usersprodi')->where('user_id',$user_id)->delete();
                $daftar_jenjang=json_decode($request->input('kode_jenjang'),true);
                foreach($daftar_jenjang as $v)
                {
                    $sql = "
                        INSERT INTO usersprodi (                    
                            user_id, 
                            kode_jenjang,
                            kode_prodi,
                            nama_prodi,
                            nama_prodi_alias,
                            kode_jenjang,
                            nama_jenjang,                                                        
                            created_at, 
                            updated_at
                        ) 
                        SELECT
                            '$user_id',                    
                            id,
                            kode_prodi,
                            nama_prodi,
                            nama_prodi_alias,
                            kode_jenjang,
                            nama_jenjang,                          
                            NOW() AS created_at,
                            NOW() AS updated_at
                        FROM prodi                    
                        WHERE 
                            id='$v' 
                    ";
                    \DB::statement($sql); 
                }

                $daftar_roles=json_decode($request->input('role_id'),true);    
                if (($key= array_search('dosen',$daftar_roles))===false)
                {                    
                    $key= array_search('dosenwali',$daftar_roles);
                    if (isset($daftar_roles[$key]))
                    {
                        unset($daftar_roles[$key]);
                    }                    
                }
                $user->syncRoles($daftar_roles);
                $dosen=UserDosen::find($user->id);

                foreach($daftar_roles as $v)
                {
                    if ($v=='dosen'||$v=='dosenwali') // sementara seperti ini karena kalau bertambah tinggal diganti
                    {              
                        $permission=Role::findByName($v)->permissions;
                        $permissions=$permission->pluck('name');
                        $user->givePermissionTo($permissions);

                        if ($v=='dosen' && is_null($dosen))
                        {
                            UserDosen::create([
                                'user_id'=>$user->id,
                                'nama_dosen'=>$request->input('name'),                                                            
                            ]);
                        }
                        else if ($v=='dosen' && !is_null($dosen))
                        {
                            $dosen->active=1;
                            $dosen->save();
                        }
                        else if (!is_null($dosen))
                        {
                            $dosen->active=0;
                            $dosen->save();
                        }
                        //set dosen wali
                        if ($v=='dosenwali' && $v=='dosen')
                        {
                            \DB::table('dosen')
                                ->where('user_id',$user->id)
                                ->update(['is_dw'=>true]);
                        }
                        else
                        {
                            \DB::table('dosen')
                                ->where('user_id',$user->id)
                                ->update(['is_dw'=>false]);
                        }
                    }
                }

                \App\Models\System\ActivityLog::log($request,[
                                                            'object' => $this->guard()->user(), 
                                                            'object_id' => $this->guard()->user()->id, 
                                                            'user_id' => $this->getUserid(), 
                                                            'message' => 'Mengubah data user PSB ('.$user->username.') berhasil'
                                                        ]);

                return $user;
                
            });

            return Response()->json([
                                    'status'=>1,
                                    'pid'=>'update',
                                    'user'=>$user,      
                                    'message'=>'Data user PSB '.$user->username.' berhasil diubah.'
                                ], 200); 
        }
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    { 
        $this->hasPermissionTo('SYSTEM-USERS-PSB_DESTROY');

        $user = User::where('isdeleted','1')
                    ->find($id); 
        
        if (is_null($user))
        {
            return Response()->json([
                                    'status'=>1,
                                    'pid'=>'destroy',                
                                    'message'=>["User ID ($id) gagal dihapus"]
                                ],422); 
        }
        else
        {
            $username=$user->username;
            $user->delete();

            \App\Models\System\ActivityLog::log($request,[
                                                                'object' => $this->guard()->user(), 
                                                                'object_id' => $this->guard()->user()->id, 
                                                                'user_id' => $this->getUserid(), 
                                                                'message' => 'Menghapus user PSB ('.$username.') berhasil'
                                                            ]);
        
            return Response()->json([
                                        'status'=>1,
                                        'pid'=>'destroy',                
                                        'message'=>"User PSB ($username) berhasil dihapus"
                                    ], 200);
        }
                  
    }
}