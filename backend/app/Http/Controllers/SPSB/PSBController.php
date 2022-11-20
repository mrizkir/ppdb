<?php

namespace App\Http\Controllers\SPSB;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use GuzzleHttp\Client;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\SPSB\FormulirPendaftaranAModel;
use App\Models\SPSB\FormulirPendaftaranBModel;
use App\Models\SPSB\FormulirPendaftaranCModel;
use App\Models\SPSB\FormulirPendaftaranDModel;
use App\Models\SPSB\PersyaratanPPDBModel;
use App\Models\System\ConfigurationModel;
use App\Models\Keuangan\KonfirmasiPembayaranModel;

use App\Helpers\Helper;
use App\Mail\SiswaBaruRegistered;
use App\Mail\VerifyEmailAddress;

use Ramsey\Uuid\Uuid;

class PSBController extends Controller {         
  /**
   * digunakan untuk mendapatkan calon peserta didik yang baru mendaftar di halaman pendaftaran
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {   
    $this->hasPermissionTo('SPSB-PSB_BROWSE');

    $this->validate($request, [           
      'TA'=>'required',
      'kode_jenjang'=>'required'
    ]);
    
    $ta=$request->input('TA');
    $kode_jenjang=$request->input('kode_jenjang');

    $data = User::where('default_role','siswabaru')
          ->select(\DB::raw('
                  users.id,
                  users.username,
                  users.name,
                  users.email,
                  users.nomor_hp,
                  users.active,
                  users.code,
                  users.foto,
                  formulir_pendaftaran_a.kode_jenjang,
                  formulir_pendaftaran_a.ta,
                  users.created_at,
                  users.updated_at'
                ))
          ->join('formulir_pendaftaran_a','formulir_pendaftaran_a.user_id','users.id')
          ->where('users.ta',$ta)
          ->where('kode_jenjang',$kode_jenjang)
          ->get();
    
    return Response()->json([
                'status'=>1,
                'pid'=>'fetchdata',
                'psb'=>$data,
                'message'=>'Fetch data calon peserta didik berhasil diperoleh'
              ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK);  
  }    
  /**
   * digunakan untuk mendapatkan calon peserta didik yang telah mengisi formulir pendaftaran
   *
   * @return \Illuminate\Http\Response
   */
  public function formulirpendaftaran(Request $request)
  {   
    $this->hasAnyPermission(['SPSB-PSB-FORMULIR-PENDAFTARAN_BROWSE','SPSB-PSB-LAPORAN-PRODI_BROWSE']);

    $this->validate($request, [           
      'TA'=>'required',
      'kode_jenjang'=>'required'
    ]);
    
    $ta=$request->input('TA');
    $kode_jenjang=$request->input('kode_jenjang');

    $data = FormulirPendaftaranAModel::select(\DB::raw('
            users.id,
            users.name,
            users.nomor_hp,
            formulir_pendaftaran_a.jk,
            formulir_pendaftaran_a.asal_sekolah,
            users.active,
            users.foto,
            users.created_at,
            users.updated_at
          '))
          ->join('users','formulir_pendaftaran_a.user_id','users.id')                                        
          ->where('users.ta',$ta)
          ->where('kode_jenjang',$kode_jenjang)                                
          ->where('users.active',1)    
          ->orderBy('users.name','ASC') 
          ->get();
    
    return Response()->json([
      'status'=>1,
      'pid'=>'fetchdata',
      'psb'=>$data,
      'message'=>'Fetch data calon peserta didik berhasil diperoleh'
    ], 200);  
  }  
  /**
   * digunakan untuk mendapatkan ayah calon peserta didik yang telah mengisi formulir pendaftaran
   *
   * @return \Illuminate\Http\Response
   */
  public function biodataayah(Request $request)
  {   
    $this->hasAnyPermission(['SPSB-PSB-FORMULIR-PENDAFTARAN_BROWSE','SPSB-PSB-LAPORAN-PRODI_BROWSE']);

    $this->validate($request, [           
      'TA'=>'required',
      'kode_jenjang'=>'required'
    ]);
    
    $ta=$request->input('TA');
    $kode_jenjang=$request->input('kode_jenjang');

    $data = FormulirPendaftaranAModel::select(\DB::raw('
            users.id,
            users.name,
            formulir_pendaftaran_c.nama_ayah,
            formulir_pendaftaran_c.nomor_hp,                        
            users.active,
            users.foto,
            users.created_at,
            users.updated_at
          '))
          ->join('users','formulir_pendaftaran_a.user_id','users.id')                                        
          ->join('formulir_pendaftaran_c','formulir_pendaftaran_c.user_id','users.id')                                        
          ->where('users.ta',$ta)
          ->where('kode_jenjang',$kode_jenjang)                                
          ->where('users.active',1)    
          ->orderBy('users.name','ASC') 
          ->get();
    
    return Response()->json([
                'status'=>1,
                'pid'=>'fetchdata',
                'psb'=>$data,
                'message'=>'Fetch data calon peserta didik berhasil diperoleh'
              ], 200); 
  }  
  /**
   * digunakan untuk mendapatkan ayah calon peserta didik yang telah mengisi formulir pendaftaran
   *
   * @return \Illuminate\Http\Response
   */
  public function biodataibu(Request $request)
  {   
    $this->hasAnyPermission(['SPSB-PSB-FORMULIR-PENDAFTARAN_BROWSE','SPSB-PSB-LAPORAN-PRODI_BROWSE']);

    $this->validate($request, [           
      'TA'=>'required',
      'kode_jenjang'=>'required'
    ]);
    
    $ta=$request->input('TA');
    $kode_jenjang=$request->input('kode_jenjang');

    $data = FormulirPendaftaranAModel::select(\DB::raw('
            users.id,
            users.name,
            formulir_pendaftaran_d.nama_ibu,
            formulir_pendaftaran_d.nomor_hp,                        
            users.active,
            users.foto,
            users.created_at,
            users.updated_at
          '))
          ->join('users','formulir_pendaftaran_a.user_id','users.id')                                        
          ->join('formulir_pendaftaran_d','formulir_pendaftaran_d.user_id','users.id')                                        
          ->where('users.ta',$ta)
          ->where('kode_jenjang',$kode_jenjang)                                
          ->where('users.active',1)    
          ->orderBy('users.name','ASC') 
          ->get();
    
    return Response()->json([
                'status'=>1,
                'pid'=>'fetchdata',
                'psb'=>$data,
                'message'=>'Fetch data calon peserta didik berhasil diperoleh'
              ], 200); 
  }  
  /**
   * digunakan untuk mendapatkan ayah calon peserta didik yang telah mengisi formulir pendaftaran
   *
   * @return \Illuminate\Http\Response
   */
  public function situasikeluarga(Request $request)
  {   
    $this->hasAnyPermission(['SPSB-PSB-FORMULIR-PENDAFTARAN_BROWSE','SPSB-PSB-LAPORAN-PRODI_BROWSE']);

    $this->validate($request, [           
      'TA'=>'required',
      'kode_jenjang'=>'required'
    ]);
    
    $ta=$request->input('TA');
    $kode_jenjang=$request->input('kode_jenjang');

    $data = FormulirPendaftaranAModel::select(\DB::raw('
      users.id,
      users.name,
      formulir_pendaftaran_b.tinggal_bersama,
      formulir_pendaftaran_b.status_pernikahan,                        
      formulir_pendaftaran_b.desc,                        
      users.active,
      users.foto,
      users.created_at,
      users.updated_at
    '))
    ->join('users','formulir_pendaftaran_a.user_id','users.id')                                        
    ->join('formulir_pendaftaran_b','formulir_pendaftaran_b.user_id','users.id')                                        
    ->where('users.ta',$ta)
    ->where('kode_jenjang',$kode_jenjang)                                
    ->where('users.active', 1)    
    ->orderBy('users.name','ASC') 
    ->get();
    
    return Response()->json([
                'status'=>1,
                'pid'=>'fetchdata',
                'psb'=>$data,
                'message'=>'Fetch data calon peserta didik berhasil diperoleh'
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
    $this->validate($request, [
      'name'=>'required',            
      'email'=>'required|string|email',
      'nomor_hp'=>'required|numeric',            
      'kode_jenjang'=>'required|numeric|exists:jenjang_studi,kode_jenjang',            
      'username'=>'required|string|unique:users',
      'password'=>'required',
      'captcha_response'=>[
                'required',
                function ($attribute, $value, $fail) 
                {
                  $client = new Client ();
                  $response = $client->post(
                    'https://www.google.com/recaptcha/api/siteverify',
                    ['form_params'=>
                      [
                        'secret'=>ConfigurationModel::getCache('CAPTCHA_PRIVATE_KEY'),
                        'response'=>$value
                      ]
                    ]);    
                  $body = json_decode((string)$response->getBody());
                  if (!$body->success)
                  {
                    $fail('Token Google Captcha, salah !!!.');
                  }
                }
              ]
    ]);
    $user = \DB::transaction(function () use ($request){
      $now = \Carbon\Carbon::now()->toDateTimeString();   
      $kode_jenjang=$request->input('kode_jenjang');
      switch($kode_jenjang)
      {
        case 1:
          $code=349000+mt_rand(1,999);
        break;
        case 2:
        case 3:                
          $code=349000+mt_rand(1,999);
        break;
        default:
          $code=0;
      }            
      $ta=ConfigurationModel::getCache('DEFAULT_TAHUN_PENDAFTARAN');
      $user=User::create([
        'id'=>Uuid::uuid4()->toString(),
        'name'=>strtoupper($request->input('name')),
        'email'=>$request->input('email'),
        'username'=> $request->input('username'),
        'password'=>Hash::make($request->input('password')),
        'nomor_hp'=>$request->input('nomor_hp'),
        'ta'=>$ta,
        'email_verified_at'=>'',
        'theme'=>'default',  
        'foto'=> 'storage/images/users/no_photo.png',
        'code'=>$code,          
        'active'=>0,          
        'default_role'=>'siswabaru',          
        'created_at'=>$now, 
        'updated_at'=>$now
      ]);            
      $role='siswabaru';   
      $user->assignRole($role);
      $permission=Role::findByName('siswabaru')->permissions;
      $user->givePermissionTo($permission->pluck('name'));             
      
      FormulirPendaftaranAModel::create([
        'user_id'=>$user->id,
        'nama_siswa'=>strtoupper($request->input('name')),                                
        'kode_jenjang'=>$kode_jenjang,
        'ta'=>$ta,
      ]);
      FormulirPendaftaranBModel::create([
        'user_id'=>$user->id,                
      ]);
      FormulirPendaftaranCModel::create([
        'user_id'=>$user->id,            
        'nomor_hp'=>$request->input('nomor_hp'),
      ]);
      FormulirPendaftaranDModel::create([
        'user_id'=>$user->id,            
        'nomor_hp'=>$request->input('nomor_hp'),
      ]);
      PersyaratanPPDBModel::create([
        'user_id'=>$user->id,                            
      ]);
      return $user;
    });
    $config_kirim_email = ConfigurationModel::getCache('EMAIL_SISWA_ISVALID');        
    if (!is_null($user) && $config_kirim_email==1)
    {
      $code='';
      app()->mailer->to($request->input('email'))->send(new VerifyEmailAddress($user->code));            
    }
    else
    {
      $code=$user->code;
    }       

    return Response()->json([
                  'status'=>1,
                  'pid'=>'store',
                  'email'=>$user->email,                              
                  'code'=>\App\Helpers\Helper::formatUang($code),    
                  'message'=>'Data Peserta Didik baru berhasil disimpan.'
                ], 200);

  }      
  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function storependaftar(Request $request)
  {
    $this->hasPermissionTo('SPSB-PSB_STORE');

    $this->validate($request, [
      'name'=>'required',            
      'email'=>'required|string|email',
      'nomor_hp'=>'required',            
      'kode_jenjang'=>'required|numeric|exists:jenjang_studi,kode_jenjang',
      'tahun_pendaftaran'=>'required|numeric',            
      'username'=>'required|string|unique:users',
      'password'=>'required',                        
    ]);
    $user = \DB::transaction(function () use ($request) {
      $now = \Carbon\Carbon::now()->toDateTimeString();                   
      $code=mt_rand(1000,9999);
      $ta=$request->input('tahun_pendaftaran');
      $user=User::create([
        'id'=>Uuid::uuid4()->toString(),
        'name'=>strtoupper($request->input('name')),
        'email'=>$request->input('email'),
        'username'=> $request->input('username'),
        'password'=>Hash::make($request->input('password')),
        'nomor_hp'=>$request->input('nomor_hp'),
        'ta'=>$ta,
        'email_verified_at'=>'',
        'theme'=>'default',  
        'code'=>$code,          
        'active'=>1,         
        'default_role'=>'siswabaru',
        'foto'=>'storage/images/users/no_photo.png', 
        'created_at'=>$now, 
        'updated_at'=>$now
      ]);            
      $role='siswabaru';   
      $user->assignRole($role);
      $permission=Role::findByName('siswabaru')->permissions;
      $user->givePermissionTo($permission->pluck('name'));             
      
      FormulirPendaftaranAModel::create([
        'user_id'=>$user->id,
        'nama_siswa'=>strtoupper($request->input('name')),                                
        'kode_jenjang'=>$request->input('kode_jenjang'),
        'ta'=>$ta,
      ]);

      return $user;
    });
    $config_kirim_email = ConfigurationModel::getCache('EMAIL_SISWA_ISVALID');        
    if (!is_null($user) && $config_kirim_email==1)
    {
      $code='';            
      app()->mailer->to($request->input('email'))->send(new VerifyEmailAddress($user->code));            
    }       
    else
    {
      $code=$user->code;
    }
    return Response()->json([
      'status'=>1,
      'pid'=>'store',
      'pendaftar'=>$user,
      'code'=>$code,                                    
      'message'=>'Data Peserta Didik baru berhasil disimpan.'
    ], 200);

  }      
  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function updatependaftar(Request $request,$id)
  {
    $this->hasPermissionTo('SPSB-PSB_UPDATE');

    $user = User::find($id);
    if (is_null($user))
    {
      return Response()->json([
                  'status'=>1,
                  'pid'=>'update',                
                  'message'=>["User ID ($id) gagal diupdate"]
                ], 422);
    }
    else
    {
      $this->validate($request, [
        'username'=>[
          'required',
          'unique:users,username,'.$user->id
        ],              
        'email'=>'required|string|email',
        'nomor_hp'=>'required',
        'kode_jenjang'=>'required|numeric|exists:jenjang_studi,kode_jenjang',
        'tahun_pendaftaran'=>'required|numeric'            
      ]);
      
      $user = \DB::transaction(function () use ($request,$user){
        $user->name = strtoupper($request->input('name'));
        $user->nisn = $request->input('nisn');
        $user->email = $request->input('email');
        $user->nomor_hp = $request->input('nomor_hp');
        $user->username = $request->input('username');        
        if (!empty(trim($request->input('password')))) {
          $user->password = Hash::make($request->input('password'));
        }
        $user->ta=$request->input('tahun_pendaftaran');
        $user->save();

        $formulir=FormulirPendaftaranAModel::find($user->id);
        $formulir->nama_siswa=strtoupper($request->input('name'));                
        $formulir->kode_jenjang=$request->input('kode_jenjang');
        $formulir->ta=$request->input('tahun_pendaftaran');
        $formulir->save();
        
        return $user;
      });
    }

    return Response()->json([
                  'status'=>1,
                  'pid'=>'store',
                  'pendaftar'=>$user,                                  
                  'message'=>'Data Peserta Didik baru berhasil diubah.'
                ], 200);

  }      
  /**
   * Detail formulir pendaftaran
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function show(Request $request,$id)
  {
    $formulir=FormulirPendaftaranAModel::select(\DB::raw('
      users.id,
      user_id,
      nama_siswa,
      nisn,
      nama_panggilan,
      jk,
      nik,
      tempat_lahir,
      tanggal_lahir,
      idagama,
      id_kebutuhan_khusus,

      address1_desa_id,
      address1_kelurahan,
      address1_kecamatan_id,
      address1_kecamatan,
      address1_kabupaten_id,
      address1_kabupaten,
      address1_provinsi_id,
      address1_provinsi,
      alamat_tempat_tinggal,
      address1_rt,
      address1_rw,
      kode_pos,
      kewarganegaraan,

      asal_sekolah,
      anak_ke,
      jumlah_saudara,
      golongan_darah,
      penyakit,
      avoid_food,
      tinggi,
      berat_badan,
      ukuran_seragam,
      id_moda,
      jarak_ke_sekolah,
      waktu_tempuh,

      kode_jenjang,
      `desc`,
      users.ta
    '))
    ->join('users','users.id','formulir_pendaftaran_a.user_id')                                            
    ->find($id);

    if (is_null($formulir))
    {
      return Response()->json([
                  'status'=>1,
                  'pid'=>'fetchdata',                
                  'message'=>["Formulir Pendaftaran dengan ID ($id) gagal diperoleh"]
                ], 422);
    }
    else
    {
      return Response()->json([
        'status'=>1,
        'pid'=>'fetchdata',                
        'formulir'=>$formulir,                
        'message'=>"Formulir Pendaftaran dengan ID ($id) berhasil diperoleh"
      ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK);        
    }

  }
  /**
   * Detail formulir pendaftaran
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function showsituasikeluarga(Request $request,$id)
  {
    $formulir=FormulirPendaftaranBModel::select(\DB::raw('
      users.id,
      user_id,
      tinggal_bersama,
      status_pernikahan,                                
      `desc`                                                               
    '))
    ->join('users','users.id','formulir_pendaftaran_b.user_id')                                            
    ->find($id);
    
    if (is_null($formulir))
    {
      return Response()->json([
                  'status'=>1,
                  'pid'=>'fetchdata',                
                  'message'=>["Formulir Pendaftaran Situasi Keluarga dengan ID ($id) gagal diperoleh"]
                ], 422);
    }
    else
    {
      return Response()->json([
                    'status'=>1,
                    'pid'=>'fetchdata',                
                    'formulir'=>$formulir,                
                    'message'=>"Formulir Pendaftaran dengan ID ($id) berhasil diperoleh"
                  ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK);        
    }

  }
  /**
   * Detail formulir pendaftaran
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function showbiodataayah(Request $request,$id)
  {
    $formulir=FormulirPendaftaranCModel::select(\DB::raw('
                                users.id,
                                user_id,
                                nama_ayah,
                                hubungan,
                                tempat_lahir,
                                tanggal_lahir,
                                idagama,
                                
                                address1_desa_id,
                                address1_kelurahan,
                                address1_kecamatan_id,
                                address1_kecamatan,
                                address1_kabupaten_id,
                                address1_kabupaten,
                                address1_provinsi_id,
                                address1_provinsi,
                                alamat_tempat_tinggal,
                                kewarganegaraan,

                                users.nomor_hp,
                                users.email,
                                pendidikan,
                                pekerjaan_instansi,
                                penghasilan_bulanan,
                                
                                `desc`                                                               
                              '))
                      ->join('users','users.id','formulir_pendaftaran_c.user_id')                                            
                      ->find($id);
    if (is_null($formulir))
    {
      return Response()->json([
                  'status'=>1,
                  'pid'=>'fetchdata',                
                  'message'=>["Formulir Pendaftaran Biodata Ayah dengan ID ($id) gagal diperoleh"]
                ], 422);
    }
    else
    {
      return Response()->json([
                    'status'=>1,
                    'pid'=>'fetchdata',                
                    'formulir'=>$formulir,                
                    'message'=>"Formulir Pendaftaran dengan ID ($id) berhasil diperoleh"
                  ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK);        
    }

  }
  /**
   * Detail formulir pendaftaran
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function showbiodataibu(Request $request,$id)
  {
    $formulir=FormulirPendaftaranDModel::select(\DB::raw('
                                users.id,
                                user_id,
                                nama_ibu,
                                hubungan,
                                tempat_lahir,
                                tanggal_lahir,
                                idagama,
                                
                                address1_desa_id,
                                address1_kelurahan,
                                address1_kecamatan_id,
                                address1_kecamatan,
                                address1_kabupaten_id,
                                address1_kabupaten,
                                address1_provinsi_id,
                                address1_provinsi,
                                alamat_tempat_tinggal,
                                kewarganegaraan,

                                formulir_pendaftaran_d.nomor_hp,
                                formulir_pendaftaran_d.email,
                                pendidikan,
                                pekerjaan_instansi,
                                penghasilan_bulanan,
                                
                                `desc`                                                               
                              '))
                      ->join('users','users.id','formulir_pendaftaran_d.user_id')                                            
                      ->find($id);
    if (is_null($formulir))
    {
      return Response()->json([
                  'status'=>1,
                  'pid'=>'fetchdata',                
                  'message'=>["Formulir Pendaftaran Biodata Ibu dengan ID ($id) gagal diperoleh"]
                ], 422);
    }
    else
    {
      return Response()->json([
                    'status'=>1,
                    'pid'=>'fetchdata',                
                    'formulir'=>$formulir,                
                    'message'=>"Formulir Pendaftaran dengan ID ($id) berhasil diperoleh"
                  ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK);        
    }

  }
  /**
   * Detail formulir pendaftaran
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function showpersyaratanppdb(Request $request,$id)
  {
    $formulir=PersyaratanPPDBModel::find($id);
    if (is_null($formulir))
    {
      return Response()->json([
                  'status'=>1,
                  'pid'=>'fetchdata',                
                  'message'=>["Formulir Pendaftaran Biodata Ibu dengan ID ($id) gagal diperoleh"]
                ], 422);
    }
    else
    {
      return Response()->json([
                    'status'=>1,
                    'pid'=>'fetchdata',                
                    'formulir'=>$formulir,                
                    'message'=>"Formulir Pendaftaran dengan ID ($id) berhasil diperoleh"
                  ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK);        
    }

  }
  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function konfirmasi(Request $request)
  {
    $this->validate($request, [            
      'username'=>'required|string|exists:users,username',            
    ]);
    $now = \Carbon\Carbon::now()->toDateTimeString();       
    $username= $request->input('username');  

    $user = \DB::table('users')->where('username',$username)->get();        

    if ($user->count()>0)
    {
      $user=User::find($user[0]->id);      
      $konfirmasi = KonfirmasiPembayaranModel::find($user->id);
      if (is_null($konfirmasi))
      {  
        return Response()->json([
                      'status'=>1,
                      'pid'=>'fetchdata',        
                      'user'=>$user,        
                      'message'=>'Peserta Didik berhasil diperoleh.'
                    ], 200);
      }
      else
      {
        return Response()->json([
                    'status'=>0,
                    'pid'=>'fetchdata',        
                    'message'=>['Calon Peserta Didik a.n ('.$user->name.') telah melakukan konfirmasi pembayaran.']
                  ],422);
      }
    }
    else
    {
      return Response()->json([
                    'status'=>0,
                    'pid'=>'fetchdata',        
                    'message'=>['Peserta Didik gagal diperoleh.']
                  ],422);
    }

  }   
  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function konfirmasipembayaran(Request $request)
  {
    $this->validate($request, [            
      'user_id'=>'required|string|exists:users,id',
      'transaksi_id'=>'required|numeric',
      'id_channel'=>'required',            
      'nomor_rekening_pengirim'=>'required|numeric',
      'nama_rekening_pengirim'=>'required',
      'nama_bank_pengirim'=>'required',
      'total_bayar'=>'required|numeric',
      'tanggal_bayar'=>'required',            
      'bukti_bayar'=>'required',            
    ]);
    $transaksi_id=$request->input('transaksi_id');        
    $bukti_bayar=$request->file('bukti_bayar');
    $mime_type=$bukti_bayar->getMimeType();
    if ($mime_type=='image/png' || $mime_type=='image/jpeg')
    {
      $folder=Helper::public_path('images/buktibayar/');
      $file_name=uniqid('img').".".$bukti_bayar->getClientOriginalExtension();

      $konfirmasi=KonfirmasiPembayaranModel::updateOrCreate([
        'user_id'=>$request->input('user_id'),
        'transaksi_id'=>$request->input('user_id'),                
        'no_transaksi'=>$transaksi_id,
        'id_channel'=>$request->input('id_channel'),
        'total_bayar'=>$request->input('total_bayar'),
        'nomor_rekening_pengirim'=>$request->input('nomor_rekening_pengirim'),
        'nama_rekening_pengirim'=>strtoupper($request->input('nama_rekening_pengirim')),
        'nama_bank_pengirim'=>strtoupper($request->input('nama_bank_pengirim')),
        'desc'=>strtoupper($request->input('desc')),
        'tanggal_bayar'=>$request->input('tanggal_bayar'),
        'bukti_bayar'=>"storage/images/buktibayar/$file_name",
      ]);
      $bukti_bayar->move($folder,$file_name);

      \App\Models\System\ActivityLog::log($request,[
                              'object' => $konfirmasi,
                              'object_id' => $konfirmasi->transaksi_id,
                              'user_id' => $request->input('user_id'),
                              'message' => 'Meng-upload bukti pembayaran dan melengkapi informasi  telah berhasil dilakukan.'
                            ]);

      return Response()->json([
                    'status'=>0,
                    'pid'=>'store',
                    'konfirmasi'=>$konfirmasi,
                    'message'=>"Konfirmasi pembayaran untuk user_id ('.$konfirmasi->user_id.')   berhasil diupload"
                  ], 200);
    }
    else
    {
      return Response()->json([
                    'status'=>1,
                    'pid'=>'store',
                    'message'=>["Extensi file yang diupload bukan jpg atau png."]
                  ],422);
    }

  }   
  /**
   * update formulir pendaftaran
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request,$id)
  {
    $formulir=FormulirPendaftaranAModel::find($id);

    if (is_null($formulir))
    {
      return Response()->json([
                  'status'=>1,
                  'pid'=>'update',                
                  'message'=>["Formulir Pendaftaran dengan ID ($id) gagal diperoleh"]
                ], 422);
    }
    else
    {
       
      $this->validate($request, [
        'nama_siswa'=>'required',            
        'nama_panggilan'=>'required',            
        'jk'=>'required',            
        'nik'=>'required|numeric',            
        'tempat_lahir'=>'required',            
        'tanggal_lahir'=>'required',
        'idagama'=>'required|numeric|exists:agama,idagama',
        'id_kebutuhan_khusus'=>'required|numeric|exists:kebutuhan_khusus,id_kebutuhan',

        'address1_provinsi_id'=>'required',
        'address1_provinsi'=>'required',
        'address1_kabupaten_id'=>'required',
        'address1_kabupaten'=>'required',
        'address1_kecamatan_id'=>'required',
        'address1_kecamatan'=>'required',
        'address1_desa_id'=>'required',
        'address1_kelurahan'=>'required',
        'alamat_tempat_tinggal'=>'required',
        'address1_rt'=>'required',
        'address1_rw'=>'required',
        'kode_pos'=>'required|max:5',
        'kewarganegaraan'=>'required|numeric|exists:negara,id',
        
        'anak_ke'=>'required',
        'jumlah_saudara'=>'required',
        'golongan_darah'=>'required',                
        
        'tinggi'=>'required',                
        'berat_badan'=>'required',                
        'ukuran_seragam'=>'required',                
        'id_moda'=>'required|numeric|exists:moda_transportasi,id_moda',                
        'jarak_ke_sekolah'=>'required',                
        'waktu_tempuh'=>'required',                
        
        'kode_jenjang'=>'required',
        
      ]);

      $data_siswa = \DB::transaction(function () use ($request,$formulir){                            
        $formulir->nama_siswa=strtoupper($request->input('nama_siswa'));
        $formulir->nisn=$request->input('nisn');
        $formulir->nama_panggilan=strtoupper($request->input('nama_panggilan'));
        $formulir->jk=$request->input('jk');
        $formulir->nik=$request->input('nik');
        $formulir->tempat_lahir=strtoupper($request->input('tempat_lahir'));
        $formulir->tanggal_lahir=$request->input('tanggal_lahir');
        $formulir->idagama=$request->input('idagama');
        $formulir->id_kebutuhan_khusus=$request->input('id_kebutuhan_khusus');
        
        $formulir->address1_desa_id=$request->input('address1_desa_id');
        $formulir->address1_kelurahan=$request->input('address1_kelurahan');
        $formulir->address1_kecamatan_id=$request->input('address1_kecamatan_id');
        $formulir->address1_kecamatan=$request->input('address1_kecamatan');
        $formulir->address1_kabupaten_id=$request->input('address1_kabupaten_id');
        $formulir->address1_kabupaten=$request->input('address1_kabupaten');
        $formulir->address1_provinsi_id=$request->input('address1_provinsi_id');
        $formulir->address1_provinsi=$request->input('address1_provinsi');
        $formulir->alamat_tempat_tinggal=strtoupper($request->input('alamat_tempat_tinggal'));
        $formulir->address1_rt=strtoupper($request->input('address1_rt'));
        $formulir->address1_rw=strtoupper($request->input('address1_rw'));
        $formulir->kode_pos=$request->input('kode_pos');
        $formulir->kewarganegaraan=$request->input('kewarganegaraan');
        
        $formulir->asal_sekolah=strtoupper($request->input('asal_sekolah'));
        $formulir->anak_ke=$request->input('anak_ke');
        $formulir->jumlah_saudara=$request->input('jumlah_saudara');
        $formulir->golongan_darah=$request->input('golongan_darah');
        $formulir->penyakit=strtoupper($request->input('penyakit'));
        $formulir->avoid_food=strtoupper($request->input('avoid_food'));
        $formulir->tinggi=$request->input('tinggi');
        $formulir->berat_badan=$request->input('berat_badan');
        $formulir->ukuran_seragam=$request->input('ukuran_seragam');
        $formulir->id_moda=$request->input('id_moda');
        $formulir->jarak_ke_sekolah=$request->input('jarak_ke_sekolah');
        $formulir->waktu_tempuh=$request->input('waktu_tempuh');
        
        $formulir->kode_jenjang=$request->input('kode_jenjang');

        $formulir->save();

        $user=$formulir->User;
        $user->name = strtoupper($request->input('nama_siswa'));                
        $user->save();    

        return $formulir;
      });
      return Response()->json([
                    'status'=>1,
                    'pid'=>'store',
                    'formulir'=>$formulir,          
                    'message'=>'Formulir Pendaftaran Peserta Didik baru berhasil diubah.'
                  ], 200);
    }
  }           
  /**
   * update formulir pendaftaran
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function updatesituasikeluarga(Request $request,$id)
  {
    $formulir=FormulirPendaftaranBModel::find($id);

    if (is_null($formulir))
    {
      return Response()->json([
        'status'=>1,
        'pid'=>'update',                
        'message'=>["Formulir Situasi Keluarga dengan ID ($id) gagal diperoleh"]
      ], 422);
    }
    else
    {       
      $this->validate($request, [
        'tinggal_bersama'=>'required',            
        'status_pernikahan'=>'required',
      ]);

      $data_siswa = \DB::transaction(function () use ($request, $formulir){                            
        $formulir->tinggal_bersama=strtoupper($request->input('tinggal_bersama'));
        $formulir->status_pernikahan=$request->input('status_pernikahan');                
        $formulir->desc=$request->input('desc');     
        $formulir->save();
        return $formulir;
      });
      return Response()->json([
        'status'=>1,
        'pid'=>'store',
        'formulir'=>$formulir,          
        'message'=>'Formulir Situasi Keluarga baru berhasil diubah.'
      ], 200);
    }
  }           
  /**
   * update formulir pendaftaran
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function updatebiodataayah(Request $request,$id)
  {
    $formulir=FormulirPendaftaranCModel::find($id);

    if (is_null($formulir))
    {
      return Response()->json([
                  'status'=>1,
                  'pid'=>'update',                
                  'message'=>["Formulir Biodata Ayah dengan ID ($id) gagal diperoleh"]
                ], 422);
    }
    else
    {
       
      $this->validate($request, [
        'nama_ayah'=>'required',            
        'hubungan'=>'required',                            
        'tempat_lahir'=>'required',            
        'tanggal_lahir'=>'required',
        'idagama'=>'required|numeric|exists:agama,idagama',

        'address1_provinsi_id'=>'required',
        'address1_provinsi'=>'required',
        'address1_kabupaten_id'=>'required',
        'address1_kabupaten'=>'required',
        'address1_kecamatan_id'=>'required',
        'address1_kecamatan'=>'required',
        'address1_desa_id'=>'required',
        'address1_kelurahan'=>'required',
        'alamat_tempat_tinggal'=>'required',                
        'kewarganegaraan'=>'required|numeric|exists:negara,id',
        
        'nomor_hp'=>'required',
        'email'=>'required',
        'pendidikan'=>'required',
        'pekerjaan_instansi'=>'required',        
        'penghasilan_bulanan'=>'required|numeric', 
      ]);

      $data_siswa = \DB::transaction(function () use ($request,$formulir){                            
        $formulir->nama_ayah=strtoupper($request->input('nama_ayah'));
        $formulir->hubungan=$request->input('hubungan');                
        $formulir->tempat_lahir=strtoupper($request->input('tempat_lahir'));
        $formulir->tanggal_lahir=$request->input('tanggal_lahir');
        $formulir->idagama=$request->input('idagama');
        
        $formulir->address1_desa_id=$request->input('address1_desa_id');
        $formulir->address1_kelurahan=$request->input('address1_kelurahan');
        $formulir->address1_kecamatan_id=$request->input('address1_kecamatan_id');
        $formulir->address1_kecamatan=$request->input('address1_kecamatan');
        $formulir->address1_kabupaten_id=$request->input('address1_kabupaten_id');
        $formulir->address1_kabupaten=$request->input('address1_kabupaten');
        $formulir->address1_provinsi_id=$request->input('address1_provinsi_id');
        $formulir->address1_provinsi=$request->input('address1_provinsi');
        $formulir->alamat_tempat_tinggal=strtoupper($request->input('alamat_tempat_tinggal'));                
        $formulir->kewarganegaraan=$request->input('kewarganegaraan');
        
        $formulir->nomor_hp=$request->input('nomor_hp');
        $formulir->email=$request->input('email');
        $formulir->pendidikan=strtoupper($request->input('pendidikan'));
        $formulir->pekerjaan_instansi=strtoupper($request->input('pekerjaan_instansi'));
        $formulir->penghasilan_bulanan=$request->input('penghasilan_bulanan');
        
        $formulir->save();

        $user=$formulir->User;
        $user->nomor_hp = $request->input('nomor_hp');                
        $user->email = $request->input('email');                
        $user->save();    

        return $formulir;
      });
      return Response()->json([
                    'status'=>1,
                    'pid'=>'store',
                    'formulir'=>$formulir,          
                    'message'=>'Formulir Biodata Ayah Wali baru berhasil diubah.'
                  ], 200);
    }
  }           
  /**
   * update formulir pendaftaran
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function updatebiodataibu(Request $request,$id)
  {
    $formulir=FormulirPendaftaranDModel::find($id);

    if (is_null($formulir))
    {
      return Response()->json([
                  'status'=>1,
                  'pid'=>'update',                
                  'message'=>["Formulir Biodata Ibu dengan ID ($id) gagal diperoleh"]
                ], 422);
    }
    else
    {
       
      $this->validate($request, [
        'nama_ibu'=>'required',            
        'hubungan'=>'required',                            
        'tempat_lahir'=>'required',            
        'tanggal_lahir'=>'required',
        'idagama'=>'required|numeric|exists:agama,idagama',

        'address1_provinsi_id'=>'required',
        'address1_provinsi'=>'required',
        'address1_kabupaten_id'=>'required',
        'address1_kabupaten'=>'required',
        'address1_kecamatan_id'=>'required',
        'address1_kecamatan'=>'required',
        'address1_desa_id'=>'required',
        'address1_kelurahan'=>'required',
        'alamat_tempat_tinggal'=>'required',                
        'kewarganegaraan'=>'required|numeric|exists:negara,id',
        
        'nomor_hp'=>'required',
        'email'=>'required',
        'pendidikan'=>'required',
        'pekerjaan_instansi'=>'required',        
        'penghasilan_bulanan'=>'required|numeric', 
      ]);

      $data_siswa = \DB::transaction(function () use ($request,$formulir){                            
        $formulir->nama_ibu=strtoupper($request->input('nama_ibu'));
        $formulir->hubungan=$request->input('hubungan');                
        $formulir->tempat_lahir=strtoupper($request->input('tempat_lahir'));
        $formulir->tanggal_lahir=$request->input('tanggal_lahir');
        $formulir->idagama=$request->input('idagama');
        
        $formulir->address1_desa_id=$request->input('address1_desa_id');
        $formulir->address1_kelurahan=$request->input('address1_kelurahan');
        $formulir->address1_kecamatan_id=$request->input('address1_kecamatan_id');
        $formulir->address1_kecamatan=$request->input('address1_kecamatan');
        $formulir->address1_kabupaten_id=$request->input('address1_kabupaten_id');
        $formulir->address1_kabupaten=$request->input('address1_kabupaten');
        $formulir->address1_provinsi_id=$request->input('address1_provinsi_id');
        $formulir->address1_provinsi=$request->input('address1_provinsi');
        $formulir->alamat_tempat_tinggal=strtoupper($request->input('alamat_tempat_tinggal'));                
        $formulir->kewarganegaraan=$request->input('kewarganegaraan');
        
        $formulir->nomor_hp=$request->input('nomor_hp');
        $formulir->email=$request->input('email');
        $formulir->pendidikan=strtoupper($request->input('pendidikan'));
        $formulir->pekerjaan_instansi=strtoupper($request->input('pekerjaan_instansi'));
        $formulir->penghasilan_bulanan=$request->input('penghasilan_bulanan');
        
        $formulir->save();             

        return $formulir;
      });
      return Response()->json([
                    'status'=>1,
                    'pid'=>'store',
                    'formulir'=>$formulir,          
                    'message'=>'Formulir Biodata Ibu Wali baru berhasil diubah.'
                  ], 200);
    }
  }           
  public function uploadfileselfi (Request $request,$id)
  {
    $formulir=PersyaratanPPDBModel::find($id);
    if (is_null($formulir))
    {
      return Response()->json([
                  'status'=>1,
                  'pid'=>'fetchdata',                
                  'message'=>["Formulir Pendaftaran dengan ID ($id) gagal diperoleh"]
                ], 422);
    }
    else
    {
      $this->validate($request, [                      
        'filefotoselfi'=>'required'                        
      ]);            
      $filefotoselfi = $request->file('filefotoselfi');
      $mime_type=$filefotoselfi->getMimeType();
      if ($mime_type=='application/pdf' || $mime_type=='image/png' || $mime_type=='image/jpeg')
      {
        $folder=\App\Helpers\Helper::public_path('persyaratanppdb/');
        $file_name=uniqid('fotoselfi_').".".$filefotoselfi->getClientOriginalExtension();
        if (is_file(\App\Helpers\Helper::public_path(str_replace('storage','',$formulir->file_fotoselfi))))                
        {
          unlink(\App\Helpers\Helper::public_path(str_replace('storage','',$formulir->file_fotoselfi)));
        }                
        $formulir->file_fotoselfi="storage/persyaratanppdb/$file_name";
        $formulir->save();
        $filefotoselfi->move($folder,$file_name);
        return Response()->json([
                      'status'=>1,
                      'pid'=>'store',
                      'formulir'=>$formulir,                
                      'message'=>"Foto Selfi berhasil diupload"
                    ], 200);   
          
      }
      else
      {
        return Response()->json([
                    'status'=>0,
                    'pid'=>'store',
                    'message'=>["Extensi file yang diupload bukan jpg atau png."]
                  ], 422);
        

      }            
    }
  }
  public function uploadfilektpayah (Request $request,$id)
  {
    $formulir=PersyaratanPPDBModel::find($id);
    if (is_null($formulir))
    {
      return Response()->json([
                  'status'=>1,
                  'pid'=>'fetchdata',                
                  'message'=>["Formulir Pendaftaran dengan ID ($id) gagal diperoleh"]
                ], 422);
    }
    else
    {
      $this->validate($request, [                      
        'filektpayah'=>'required'                        
      ]);            
      $filektpayah = $request->file('filektpayah');
      $mime_type=$filektpayah->getMimeType();
      if ($mime_type=='application/pdf' || $mime_type=='image/png' || $mime_type=='image/jpeg')
      {
        $folder=\App\Helpers\Helper::public_path('persyaratanppdb/');
        $file_name=uniqid('ktp_').".".$filektpayah->getClientOriginalExtension();
        if (is_file(\App\Helpers\Helper::public_path(str_replace('storage','',$formulir->file_ktp_ayah))))                
        {
          unlink(\App\Helpers\Helper::public_path(str_replace('storage','',$formulir->file_ktp_ayah)));
        }                
        $formulir->file_ktp_ayah="storage/persyaratanppdb/$file_name";
        $formulir->save();
        $filektpayah->move($folder,$file_name);
        return Response()->json([
                      'status'=>1,
                      'pid'=>'store',       
                      'formulir'=>$formulir,                
                      'message'=>"File KTP Ayah Wali berhasil diupload"
                    ], 200);   
          
      }
      else
      {
        return Response()->json([
                    'status'=>0,
                    'pid'=>'store',
                    'message'=>["Extensi file yang diupload bukan jpg atau png."]
                  ], 422);
        

      }            
    }
  }
  public function uploadfilektpibu (Request $request,$id)
  {
    $formulir=PersyaratanPPDBModel::find($id);
    if (is_null($formulir))
    {
      return Response()->json([
                  'status'=>1,
                  'pid'=>'fetchdata',                
                  'message'=>["Formulir Pendaftaran dengan ID ($id) gagal diperoleh"]
                ], 422);
    }
    else
    {
      $this->validate($request, [                      
        'filektpibu'=>'required'                        
      ]);            
      $filektpibu = $request->file('filektpibu');
      $mime_type=$filektpibu->getMimeType();
      if ($mime_type=='application/pdf' || $mime_type=='image/png' || $mime_type=='image/jpeg')
      {
        $folder=\App\Helpers\Helper::public_path('persyaratanppdb/');
        $file_name=uniqid('ktp_').".".$filektpibu->getClientOriginalExtension();
        if (is_file(\App\Helpers\Helper::public_path(str_replace('storage','',$formulir->file_ktp_ibu))))                
        {
          unlink(\App\Helpers\Helper::public_path(str_replace('storage','',$formulir->file_ktp_ibu)));
        }                
        $formulir->file_ktp_ibu="storage/persyaratanppdb/$file_name";
        $formulir->save();
        $filektpibu->move($folder,$file_name);
        return Response()->json([
                      'status'=>1,
                      'pid'=>'store',       
                      'formulir'=>$formulir,                
                      'message'=>"File KTP Ibu Wali berhasil diupload"
                    ], 200);   
          
      }
      else
      {
        return Response()->json([
                    'status'=>0,
                    'pid'=>'store',
                    'message'=>["Extensi file yang diupload bukan jpg atau png."]
                  ], 422);
        

      }            
    }
  }
  public function uploadfilekk (Request $request,$id)
  {
    $formulir=PersyaratanPPDBModel::find($id);
    if (is_null($formulir))
    {
      return Response()->json([
                  'status'=>1,
                  'pid'=>'fetchdata',                
                  'message'=>["Formulir Pendaftaran dengan ID ($id) gagal diperoleh"]
                ], 422);
    }
    else
    {
      $this->validate($request, [                      
        'filekk'=>'required'                        
      ]);            
      $filekk = $request->file('filekk');
      $mime_type=$filekk->getMimeType();
      if ($mime_type=='application/pdf' || $mime_type=='image/png' || $mime_type=='image/jpeg')
      {
        $folder=\App\Helpers\Helper::public_path('persyaratanppdb/');
        $file_name=uniqid('kk_').".".$filekk->getClientOriginalExtension();
        if (is_file(\App\Helpers\Helper::public_path(str_replace('storage','',$formulir->file_kk))))                
        {
          unlink(\App\Helpers\Helper::public_path(str_replace('storage','',$formulir->file_kk)));
        }                
        $formulir->file_kk="storage/persyaratanppdb/$file_name";
        $formulir->save();
        $filekk->move($folder,$file_name);
        return Response()->json([
                      'status'=>1,
                      'pid'=>'store',       
                      'formulir'=>$formulir,                
                      'message'=>"File KK berhasil diupload"
                    ], 200);   
          
      }
      else
      {
        return Response()->json([
                    'status'=>0,
                    'pid'=>'store',
                    'message'=>["Extensi file yang diupload bukan jpg atau png."]
                  ], 422);
        

      }            
    }
  }
  public function uploadfileaktalahir (Request $request,$id)
  {
    $formulir=PersyaratanPPDBModel::find($id);
    if (is_null($formulir))
    {
      return Response()->json([
                  'status'=>1,
                  'pid'=>'fetchdata',                
                  'message'=>["Formulir Pendaftaran dengan ID ($id) gagal diperoleh"]
                ], 422);
    }
    else
    {
      $this->validate($request, [                      
        'fileaktalahir'=>'required'                        
      ]);            
      $fileaktalahir = $request->file('fileaktalahir');
      $mime_type=$fileaktalahir->getMimeType();
      if ($mime_type=='application/pdf' || $mime_type=='image/png' || $mime_type=='image/jpeg')
      {
        $folder=\App\Helpers\Helper::public_path('persyaratanppdb/');
        $file_name=uniqid('aktalahir_').".".$fileaktalahir->getClientOriginalExtension();
        if (is_file(\App\Helpers\Helper::public_path(str_replace('storage','',$formulir->file_aktalahir))))                
        {
          unlink(\App\Helpers\Helper::public_path(str_replace('storage','',$formulir->file_aktalahir)));
        }                
        $formulir->file_aktalahir="storage/persyaratanppdb/$file_name";
        $formulir->save();
        $fileaktalahir->move($folder,$file_name);
        return Response()->json([
                      'status'=>1,
                      'pid'=>'store',       
                      'formulir'=>$formulir,                
                      'message'=>"File KK berhasil diupload"
                    ], 200);   
          
      }
      else
      {
        return Response()->json([
                    'status'=>0,
                    'pid'=>'store',
                    'message'=>["Extensi file yang diupload bukan jpg atau png."]
                  ], 422);
        

      }            
    }
  }
  /**
   * Menghapus calon peserta didik baru
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function destroy(Request $request,$id)
  { 
    $this->hasPermissionTo('SPSB-PSB_DESTROY');

    $user = User::where('isdeleted','1')
          ->find($id); 
    
    if (is_null($user))
    {
      return Response()->json([
                  'status'=>1,
                  'pid'=>'destroy',                
                  'message'=>["Calon Peserta Didik Baru dengan ID ($id) gagal dihapus"]
                ], 422);
    }
    else
    {
      $name=$user->name;
      $user->delete();

      \App\Models\System\ActivityLog::log($request,[
                                'object' => $this->guard()->user(), 
                                'object_id' => $this->guard()->user()->id, 
                                'user_id' => $this->getUserid(), 
                                'message' => 'Menghapus Peserta Didik Baru ('.$name.') berhasil'
                              ]);
    
      return Response()->json([
                    'status'=>1,
                    'pid'=>'destroy',                
                    'message'=>"Peserta Didik Baru ($name) berhasil dihapus"
                  ], 200);        
    }
          
  }      
}