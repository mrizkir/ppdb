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
use App\Models\SPSB\FormulirPendaftaranEModel;
use App\Models\SPSB\FormulirPendaftaranFModel;
use App\Models\SPSB\PersyaratanPPDBModel;
use App\Models\System\ConfigurationModel;
use App\Models\Keuangan\KonfirmasiPembayaranModel;

use App\Helpers\HelperPendaftaran;
use App\Mail\SiswaBaruRegistered;
use App\Mail\VerifyEmailAddress;

use Ramsey\Uuid\Uuid;

use Exception;

class PSBController extends Controller 
{
  private function checkUsia($request)
  {
    $tanggal_lahir = $request->input('tanggal_lahir');
    $batas_tanggal = date('2025-07-01');
    $usia = \App\Helpers\Helper::getUsia($tanggal_lahir, $batas_tanggal);
    $kode_jenjang = $request->input('kode_jenjang');
    
    switch($kode_jenjang)
    {
      case 1:
        if($usia < 4.4 || $usia > 6.0 )
        {
          throw new Exception("Usia siswa TK ($usia) diluar batas yang telah ditetapkan (4.4 s.d 6).");          
        }
      break;
      case 2:
        if($usia < 6.0 || $usia > 9.0 )
        {
          throw new Exception("Usia siswa SD ($usia) diluar batas yang telah ditetapkan (6 s.d 9).");          
        }
      break;
      case 3:
        if($usia < 10.0 || $usia > 15.0 )
        {
          throw new Exception("Usia siswa SMP ($usia) diluar batas yang telah ditetapkan (10 s.d 15).");          
        }
      break;
      case 4:
        if($usia < 13.0 || $usia > 17.0 )
        {
          throw new Exception("Usia siswa SMA ($usia) diluar batas yang telah ditetapkan (13 s.d 17).");          
        }
      break;
    }
  }
  /**
   * digunakan untuk mendapatkan calon peserta didik yang baru mendaftar di halaman pendaftaran
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {   
    $this->hasPermissionTo('SPSB-PSB_BROWSE');

    $this->validate($request, [           
      'TA' => 'required',
      'kode_jenjang' => 'required'
    ]);
    
    $ta=$request->input('TA');
    $kode_jenjang=$request->input('kode_jenjang');

    $data = User::where('default_role', 'siswabaru')
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
    ->join('formulir_pendaftaran_a', 'formulir_pendaftaran_a.user_id', 'users.id')
    ->where('users.ta', $ta)
    ->where('kode_jenjang', $kode_jenjang)
    ->orderBy('created_at', 'desc')
    ->get();
    
    return Response()->json([
      'status' => 1,
      'pid' => 'fetchdata',
      'psb' => $data,
      'message' => 'Fetch data calon peserta didik berhasil diperoleh'
    ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK);  
  }    
  /**
   * digunakan untuk mendapatkan calon peserta didik yang telah mengisi formulir pendaftaran
   *
   * @return \Illuminate\Http\Response
   */
  public function formulirpendaftaran(Request $request)
  {   
    $this->hasAnyPermission(['SPSB-PSB-FORMULIR-PENDAFTARAN_BROWSE', 'SPSB-PSB-LAPORAN-PRODI_BROWSE']);

    $this->validate($request, [           
      'TA' => 'required',
      'kode_jenjang' => 'required'
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
    ->join('users', 'formulir_pendaftaran_a.user_id', 'users.id')                                        
    ->where('users.ta', $ta)
    ->where('kode_jenjang', $kode_jenjang)                                
    ->where('users.active', 1)    
    ->orderBy('users.name', 'ASC') 
    ->get();
    
    return Response()->json([
      'status' => 1,
      'pid' => 'fetchdata',
      'psb' => $data,
      'message' => 'Fetch data calon peserta didik berhasil diperoleh'
    ], 200);  
  }  
  /**
   * digunakan untuk mendapatkan ayah calon peserta didik yang telah mengisi formulir pendaftaran
   *
   * @return \Illuminate\Http\Response
   */
  public function biodataayah(Request $request)
  {   
    $this->hasAnyPermission(['SPSB-PSB-FORMULIR-PENDAFTARAN_BROWSE', 'SPSB-PSB-LAPORAN-PRODI_BROWSE']);

    $this->validate($request, [           
      'TA' => 'required',
      'kode_jenjang' => 'required'
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
    ->join('users', 'formulir_pendaftaran_a.user_id', 'users.id')                                        
    ->join('formulir_pendaftaran_c', 'formulir_pendaftaran_c.user_id', 'users.id')                                        
    ->where('users.ta', $ta)
    ->where('kode_jenjang', $kode_jenjang)                                
    ->where('users.active', 1)    
    ->orderBy('users.name', 'ASC') 
    ->get();
    
    return Response()->json([
      'status' => 1,
      'pid' => 'fetchdata',
      'psb' => $data,
      'message' => 'Fetch data calon peserta didik berhasil diperoleh'
    ], 200); 
  }  
  /**
   * digunakan untuk mendapatkan ayah calon peserta didik yang telah mengisi formulir pendaftaran
   *
   * @return \Illuminate\Http\Response
   */
  public function biodataibu(Request $request)
  {   
    $this->hasAnyPermission(['SPSB-PSB-FORMULIR-PENDAFTARAN_BROWSE', 'SPSB-PSB-LAPORAN-PRODI_BROWSE']);

    $this->validate($request, [           
      'TA' => 'required',
      'kode_jenjang' => 'required'
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
    ->join('users', 'formulir_pendaftaran_a.user_id', 'users.id')                                        
    ->join('formulir_pendaftaran_d', 'formulir_pendaftaran_d.user_id', 'users.id')                                        
    ->where('users.ta', $ta)
    ->where('kode_jenjang', $kode_jenjang)                                
    ->where('users.active', 1)    
    ->orderBy('users.name', 'ASC') 
    ->get();
    
    return Response()->json([
      'status' => 1,
      'pid' => 'fetchdata',
      'psb' => $data,
      'message' => 'Fetch data calon peserta didik berhasil diperoleh'
    ], 200); 
  }  
  /**
   * digunakan untuk mendapatkan ayah calon peserta didik yang telah mengisi formulir pendaftaran
   *
   * @return \Illuminate\Http\Response
   */
  public function situasikeluarga(Request $request)
  {   
    $this->hasAnyPermission(['SPSB-PSB-FORMULIR-PENDAFTARAN_BROWSE', 'SPSB-PSB-LAPORAN-PRODI_BROWSE']);

    $this->validate($request, [           
      'TA' => 'required',
      'kode_jenjang' => 'required'
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
    ->join('users', 'formulir_pendaftaran_a.user_id', 'users.id')                                        
    ->join('formulir_pendaftaran_b', 'formulir_pendaftaran_b.user_id', 'users.id')                                        
    ->where('users.ta', $ta)
    ->where('kode_jenjang', $kode_jenjang)                                
    ->where('users.active', 1)    
    ->orderBy('users.name', 'ASC') 
    ->get();
    
    return Response()->json([
      'status' => 1,
      'pid' => 'fetchdata',
      'psb' => $data,
      'message' => 'Fetch data calon peserta didik berhasil diperoleh'
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
      'name' => 'required',
      'tanggal_lahir' => 'required|date_format:Y-m-d',
      'jk' => 'required|in:L,P',
      'email' => 'required|email',
      'nomor_hp' => 'required|numeric',            
      'kode_jenjang' => 'required|numeric|exists:jenjang_studi,kode_jenjang',            
      'username' => 'required|unique:users',
      'password' => 'required',
      'penyandang_disabilitas' => 'required|in:0,1',
      'captcha_response' => [
        'required',
        function ($attribute, $value, $fail) 
        {
          $client = new Client ();
          $response = $client->post(
            'https://www.google.com/recaptcha/api/siteverify',
            ['form_params' => 
              [
                'secret' => ConfigurationModel::getCache('CAPTCHA_PRIVATE_KEY'),
                'response' => $value
              ]
            ]);    
          $body = json_decode((string)$response->getBody());
          if (!$body->success)
          {
            $fail('Token Google Captcha, salah !!!.');
          }
        }
      ]
    ], [
      'name.required' => 'Mohon nama untuk diisi',
      'tanggal_lahir.required' => 'Mohon tanggal lahir untuk diisi',
      'tanggal_lahir.date_format' => 'Format tanggal terdapat tidak sesuai. Contoh yang benar(2024-03-03).',
      'jk.required' => 'Mohon diisi jenis kelamin',
      'jk.in' => 'Nilai jenis kelamin diantara L,P',  
      'email.required' => 'Mohon email untuk diisi',
      'email.email' => 'Format email terdapat kesalahan',
      'nomor_hp.required' => 'Mohon Nomor HP/WA untuk diisi',      
      'nomor_hp.numeric' => 'Mohon Nomor HP/WA diisi dengan angka saja.',      
      'kode_jenjang.required' => 'Mohon Kode jenjang untuk diisi',
      'kode_jenjang.numeric' => 'Mohon Kode jenjang untuk diisi dengan format numeric',
      'kode_jenjang.exists' => 'Kode jenjang tidak terdaftar di tabel jenjang studi',
      'username.required' => 'Mohon username untuk diisi',
      'username.unique' => 'Username sudah tidak tersedia, silahkan ganti dengan yang lain',
      'penyandang_disabilitas.required' => 'Mohon diisi status penyandang disabilitas',
      'penyandang_disabilitas.in' => 'Nilai penyandang disabilitas diantara 0,1',  
      'captcha_response.required' => 'Mohon isi captcha response',
    ]);

    try
    {
      $penyandang_disabilitas = $request->input('penyandang_disabilitas');
      if ($penyandang_disabilitas == 1)
      {
        throw new Exception ("Pendaftaran gagal dilakukan karena menyandang Disabilitas. Silahkan hubungi Admin Sekolah Islam De Green Camp.");
      }  

      $tahun_pendaftaran = ConfigurationModel::getCache('DEFAULT_TAHUN_PENDAFTARAN');
      $kode_jenjang = $request->input('kode_jenjang');
    
      //cek usia
      $this->checkUsia($request);

      //cek kuota    
      if (!HelperPendaftaran::checkKuotaPendaftaran($tahun_pendaftaran, $kode_jenjang, $request->input('jk')))
      {
        return Response()->json([
          'status' => 0,
          'pid' => 'store',        
          'message' => 'Proses pendaftaran gagal karena kuota pendaftaran habis.'
        ], 422);
      }

      //cek total bayar
      $kombi = \DB::table('pe3_kombi_periode')
      ->where('kombi_id', 101)
      ->where('kode_jenjang', $kode_jenjang)
      ->where('tahun', $tahun_pendaftaran)
      ->where('biaya', '>', 0)
      ->first();
      
      if(is_null($kombi) )
      { 
        return Response()->json([
          'status' => 0,
          'pid' => 'store',        
          'message' => "Biaya pendaftaran jenjang pendidikan ($kode_jenjang) belum ditentukan oleh Admin.",
          'kombi' => $kombi,
        ], 422);
      }
      $user = \DB::transaction(function () use ($request, $kombi){
        $now = \Carbon\Carbon::now()->toDateTimeString();   
        $kode_jenjang=$request->input('kode_jenjang');
        switch($kode_jenjang)
        {
          case 1:
            $code = $kombi->biaya + mt_rand(1,999);
          break;
          case 2:
          case 3:                
            $code = $kombi->biaya + mt_rand(1,999);
          break;
          case 4:                
            $code = $kombi->biaya + mt_rand(1,999);
          break;
          default:
            $code=0;
        }            
        $ta = ConfigurationModel::getCache('DEFAULT_TAHUN_PENDAFTARAN');
        $user = User::create([
          'id' => Uuid::uuid4()->toString(),
          'name' => strtoupper($request->input('name')),
          'email' => $request->input('email'),
          'username' =>  $request->input('username'),
          'password' => Hash::make($request->input('password')),
          'nomor_hp' => $request->input('nomor_hp'),
          'ta' => $ta,
          'email_verified_at' => '',
          'theme' => 'default',  
          'foto' =>  'images/users/no_photo.png',
          'code' => $code,          
          'active' => 0,          
          'default_role' => 'siswabaru',          
          'created_at' => $now, 
          'updated_at' => $now
        ]);
        $role='siswabaru';   
        $user->assignRole($role);
        $permission=Role::findByName('siswabaru')->permissions;
        $user->givePermissionTo($permission->pluck('name')); 
        
        FormulirPendaftaranAModel::create([
          'user_id' => $user->id,
          'nama_siswa' => strtoupper($request->input('name')),    
          'tanggal_lahir' => $request->input('tanggal_lahir'),                            
          'jk' => strtoupper($request->input('jk')),                                
          'kode_jenjang' => $kode_jenjang,
          'ta' => $ta,
        ]);
        FormulirPendaftaranBModel::create([
          'user_id' => $user->id,                
        ]);
        FormulirPendaftaranCModel::create([
          'user_id' => $user->id,            
          'nomor_hp' => $request->input('nomor_hp'),
        ]);
        FormulirPendaftaranDModel::create([
          'user_id' => $user->id,            
          'nomor_hp' => $request->input('nomor_hp'),
        ]);
        FormulirPendaftaranEModel::create([
          'user_id' => $user->id,            
          'nomor_hp' => $request->input('nomor_hp'),
        ]);
        FormulirPendaftaranFModel::create([
          'user_id' => $user->id,            
          'nomor_hp' => $request->input('nomor_hp'),
        ]);
        PersyaratanPPDBModel::create([
          'user_id' => $user->id,                            
        ]);
        return $user;
      });
      $config_kirim_email = ConfigurationModel::getCache('EMAIL_SISWA_ISVALID');
      if (!is_null($user) && $config_kirim_email==1)
      {
        $code = '';
        app()->mailer->to($request->input('email'))->send(new VerifyEmailAddress($user->code));
      }
      else
      {
        $code = $user->code;
      }       

      return Response()->json([
        'status' => 1,
        'pid' => 'store',
        'email' => $user->email,                              
        'code' => HelperPendaftaran::formatUang($code),    
        'message' => 'Data Peserta Didik baru berhasil disimpan.'
      ], 200);
    }
    catch (Exception $e)
    {
      return Response()->json([
        'status' => 0,
        'pid' => 'store',        
        'message' => $e->getMessage(),
      ], 422);
    }   
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

    try
    {
      $this->validate($request, [
        'name' => 'required',            
        'tanggal_lahir' => 'required|date_format:Y-m-d',
        'email' => 'required|string|email',
        'nomor_hp' => 'required',            
        'kode_jenjang' => 'required|numeric|exists:jenjang_studi,kode_jenjang',
        'tahun_pendaftaran' => 'required|numeric',            
        'username' => 'required|string|unique:users',
        'password' => 'required',                        
      ]);

      //cek usia
      $this->checkUsia($request);

      $user = \DB::transaction(function () use ($request) {
        $now = \Carbon\Carbon::now()->toDateTimeString();       
        $code=mt_rand(1000,9999);
        $ta=$request->input('tahun_pendaftaran');
        $user=User::create([
          'id' => Uuid::uuid4()->toString(),
          'name' => strtoupper($request->input('name')),
          'email' => $request->input('email'),
          'username' =>  $request->input('username'),
          'password' => Hash::make($request->input('password')),
          'nomor_hp' => $request->input('nomor_hp'),
          'ta' => $ta,
          'email_verified_at' => '',
          'theme' => 'default',  
          'code' => $code,          
          'active' => 1,         
          'default_role' => 'siswabaru',
          'foto' => 'images/users/no_photo.png', 
          'created_at' => $now, 
          'updated_at' => $now
        ]);
        $role='siswabaru';   
        $user->assignRole($role);
        $permission=Role::findByName('siswabaru')->permissions;
        $user->givePermissionTo($permission->pluck('name')); 
        
        FormulirPendaftaranAModel::create([
          'user_id' => $user->id,
          'nama_siswa' => strtoupper($request->input('name')),                                
          'tanggal_lahir' => $request->input('tanggal_lahir'),
          'kode_jenjang' => $request->input('kode_jenjang'),
          'ta' => $ta,
        ]);
        
        FormulirPendaftaranBModel::create([
          'user_id' => $user->id,                
        ]);
        FormulirPendaftaranCModel::create([
          'user_id' => $user->id,            
          'nomor_hp' => $request->input('nomor_hp'),
        ]);
        FormulirPendaftaranDModel::create([
          'user_id' => $user->id,            
          'nomor_hp' => $request->input('nomor_hp'),
        ]);
        FormulirPendaftaranEModel::create([
          'user_id' => $user->id,            
          'nomor_hp' => $request->input('nomor_hp'),
        ]);
        FormulirPendaftaranFModel::create([
          'user_id' => $user->id,            
          'nomor_hp' => $request->input('nomor_hp'),
        ]);
        PersyaratanPPDBModel::create([
          'user_id' => $user->id,                            
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
        'status' => 1,
        'pid' => 'store',
        'pendaftar' => $user,
        'code' => $code,                                    
        'message' => 'Data Peserta Didik baru berhasil disimpan.'
      ], 200);
    }
    catch(Exception $e)
    {
      return Response()->json([
        'status' => 0,
        'pid' => 'store',        
        'message' => $e->getMessage(),
      ], 422);
    }
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
        'status' => 1,
        'pid' => 'update',                
        'message' => ["User ID ($id) gagal diupdate"]
      ], 422);
    }
    else
    {
      $this->validate($request, [
        'username' => [
          'required',
          'unique:users,username,'.$user->id
        ],              
        'email' => 'required|string|email',
        'nomor_hp' => 'required',
        'kode_jenjang' => 'required|numeric|exists:jenjang_studi,kode_jenjang',
        'tahun_pendaftaran' => 'required|numeric'            
      ]);
      
      $user = \DB::transaction(function () use ($request,$user){
        $user->name = strtoupper($request->input('name'));
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
      'status' => 1,
      'pid' => 'store',
      'pendaftar' => $user,                                  
      'message' => 'Data Peserta Didik baru berhasil diubah.'
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
      COALESCE(sibling_tk,"") AS sibling_tk,
      COALESCE(sibling_sd,"") AS sibling_sd,
      COALESCE(sibling_smp,"") AS sibling_smp,
      COALESCE(sibling_sma,"") AS sibling_sma,      
      kode_jenjang,
      `desc`,
      users.ta
    '))
    ->join('users', 'users.id', 'formulir_pendaftaran_a.user_id')                                            
    ->find($id);

    if (is_null($formulir))
    {
      return Response()->json([
        'status' => 1,
        'pid' => 'fetchdata',                
        'message' => ["Formulir Pendaftaran dengan ID ($id) gagal diperoleh"]
      ], 422);
    }
    else
    {
      return Response()->json([
        'status' => 1,
        'pid' => 'fetchdata',                
        'formulir' => $formulir,                
        'message' => "Formulir Pendaftaran dengan ID ($id) berhasil diperoleh"
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
    ->join('users', 'users.id', 'formulir_pendaftaran_b.user_id')                                            
    ->find($id);
    
    if (is_null($formulir))
    {
      return Response()->json([
        'status' => 1,
        'pid' => 'fetchdata',                
        'message' => ["Formulir Pendaftaran Situasi Keluarga dengan ID ($id) gagal diperoleh"]
      ], 422);
    }
    else
    {
      return Response()->json([
        'status' => 1,
        'pid' => 'fetchdata',                
        'formulir' => $formulir,                
        'message' => "Formulir Pendaftaran dengan ID ($id) berhasil diperoleh"
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
      fb_account,
      ig_account,
      tiktok_account,
      `desc`                                                               
    '))
    ->join('users', 'users.id', 'formulir_pendaftaran_c.user_id')                                            
    ->find($id);
    if (is_null($formulir))
    {
      return Response()->json([
        'status' => 1,
        'pid' => 'fetchdata',                
        'message' => ["Formulir Pendaftaran Biodata Ayah dengan ID ($id) gagal diperoleh"]
      ], 422);
    }
    else
    {
      return Response()->json([
        'status' => 1,
        'pid' => 'fetchdata',                
        'formulir' => $formulir,                
        'message' => "Formulir Pendaftaran dengan ID ($id) berhasil diperoleh"
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
      fb_account,
      ig_account,
      tiktok_account,
      `desc`                                                               
    '))
    ->join('users', 'users.id', 'formulir_pendaftaran_d.user_id')                                            
    ->find($id);

    if (is_null($formulir))
    {
      return Response()->json([
        'status' => 1,
        'pid' => 'fetchdata',                
        'message' => ["Formulir Pendaftaran Biodata Ibu dengan ID ($id) gagal diperoleh"]
      ], 422);
    }
    else
    {
      return Response()->json([
        'status' => 1,
        'pid' => 'fetchdata',                
        'formulir' => $formulir,                
        'message' => "Formulir Pendaftaran dengan ID ($id) berhasil diperoleh"
      ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK);
    }

  }
  /**
   * Detail formulir pendaftaran
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function showbiodatawali(Request $request,$id)
  {
    $formulir=FormulirPendaftaranFModel::select(\DB::raw('
      users.id,
      user_id,
      nama_wali,
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

      formulir_pendaftaran_f.nomor_hp,
      formulir_pendaftaran_f.email,
      pendidikan,
      pekerjaan_instansi,
      penghasilan_bulanan,
      fb_account,
      ig_account,
      tiktok_account,
      `desc`                                                               
    '))
    ->join('users', 'users.id', 'formulir_pendaftaran_f.user_id')                                            
    ->find($id);

    if (is_null($formulir))
    {
      return Response()->json([
        'status' => 1,
        'pid' => 'fetchdata',                
        'message' => ["Formulir Pendaftaran Biodata Wali dengan ID ($id) gagal diperoleh"]
      ], 422);
    }
    else
    {
      return Response()->json([
        'status' => 1,
        'pid' => 'fetchdata',                
        'formulir' => $formulir,                
        'message' => "Formulir Pendaftaran dengan ID ($id) berhasil diperoleh"
      ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK);
    }
  }
  /**
   * Detail formulir pendaftaran
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function showkontakdarurat(Request $request,$id)
  {
    $formulir=FormulirPendaftaranEModel::select(\DB::raw('
      users.id,
      formulir_pendaftaran_e.user_id,
      formulir_pendaftaran_e.nama_kontak,
      formulir_pendaftaran_e.hubungan,      
      formulir_pendaftaran_e.alamat_kontak,
      formulir_pendaftaran_e.nomor_hp
    '))
    ->join('users', 'users.id', 'formulir_pendaftaran_e.user_id')                                            
    ->find($id);

    if (is_null($formulir))
    {
      return Response()->json([
        'status' => 1,
        'pid' => 'fetchdata',                
        'message' => ["Formulir Pendaftaran Kontak Darurat dengan ID ($id) gagal diperoleh"]
      ], 422);
    }
    else
    {
      return Response()->json([
        'status' => 1,
        'pid' => 'fetchdata',                
        'formulir' => $formulir,                
        'message' => "Formulir Pendaftaran dengan ID ($id) berhasil diperoleh"
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
        'status' => 1,
        'pid' => 'fetchdata',                
        'message' => ["Formulir Pendaftaran Biodata Ibu dengan ID ($id) gagal diperoleh"]
      ], 422);
    }
    else
    {
      return Response()->json([
        'status' => 1,
        'pid' => 'fetchdata',                
        'formulir' => $formulir,                
        'message' => "Formulir Pendaftaran dengan ID ($id) berhasil diperoleh"
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
      'username' => 'required|string|exists:users,username',            
    ]);
    $now = \Carbon\Carbon::now()->toDateTimeString();       
    $username= $request->input('username');  

    $user = \DB::table('users')->where('username', $username)->get();

    if ($user->count()>0)
    {
      $user=User::find($user[0]->id);      
      $konfirmasi = KonfirmasiPembayaranModel::find($user->id);
      if (is_null($konfirmasi))
      {  
        return Response()->json([
          'status' => 1,
          'pid' => 'fetchdata',        
          'user' => $user,        
          'message' => 'Peserta Didik berhasil diperoleh.'
        ], 200);
      }
      else
      {
        return Response()->json([
          'status' => 0,
          'pid' => 'fetchdata',        
          'message' => ['Calon Peserta Didik a.n ('.$user->name.') telah melakukan konfirmasi pembayaran.']
        ],422);
      }
    }
    else
    {
      return Response()->json([
        'status' => 0,
        'pid' => 'fetchdata',        
        'message' => ['Peserta Didik gagal diperoleh.']
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
      'user_id' => 'required|string|exists:users,id',
      'transaksi_id' => 'required|numeric',
      'id_channel' => 'required',            
      'nomor_rekening_pengirim' => 'required|numeric',
      'nama_rekening_pengirim' => 'required',
      'nama_bank_pengirim' => 'required',
      'total_bayar' => 'required|numeric',
      'tanggal_bayar' => 'required',            
      'bukti_bayar' => 'required',            
    ]);
    $transaksi_id=$request->input('transaksi_id');
    $bukti_bayar=$request->file('bukti_bayar');
    $mime_type=$bukti_bayar->getMimeType();
    if ($mime_type=='image/png' || $mime_type=='image/jpeg')
    {
      $folder=HelperPendaftaran::public_path('images/buktibayar/');
      $file_name=uniqid('img').".".$bukti_bayar->getClientOriginalExtension();

      $konfirmasi=KonfirmasiPembayaranModel::updateOrCreate([
        'user_id' => $request->input('user_id'),
        'transaksi_id' => $request->input('user_id'),                
        'no_transaksi' => $transaksi_id,
        'id_channel' => $request->input('id_channel'),
        'total_bayar' => $request->input('total_bayar'),
        'nomor_rekening_pengirim' => $request->input('nomor_rekening_pengirim'),
        'nama_rekening_pengirim' => strtoupper($request->input('nama_rekening_pengirim')),
        'nama_bank_pengirim' => strtoupper($request->input('nama_bank_pengirim')),
        'desc' => strtoupper($request->input('desc')),
        'tanggal_bayar' => $request->input('tanggal_bayar'),
        'bukti_bayar' => "images/buktibayar/$file_name",
      ]);
      $bukti_bayar->move($folder,$file_name);

      \App\Models\System\ActivityLog::log($request,[
        'object' => $konfirmasi,
        'object_id' => $konfirmasi->transaksi_id,
        'user_id' => $request->input('user_id'),
        'message' => 'Meng-upload bukti pembayaran dan melengkapi informasi  telah berhasil dilakukan.'
      ]);

      return Response()->json([
        'status' => 0,
        'pid' => 'store',
        'konfirmasi' => $konfirmasi,
        'message' => "Konfirmasi pembayaran untuk user_id ('.$konfirmasi->user_id.')   berhasil diupload"
      ], 200);
    }
    else
    {
      return Response()->json([
        'status' => 1,
        'pid' => 'store',
        'message' => ["Extensi file yang diupload bukan jpg atau png."]
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
    try
    {
      $formulir = FormulirPendaftaranAModel::find($id);

      if (is_null($formulir))
      {
        throw new Exception ("Formulir Pendaftaran dengan ID ($id) gagal diperoleh");
      }  

      $this->validate($request, [
        'nama_siswa' => 'required',            
        'nama_panggilan' => 'required',            
        'jk' => 'required',            
        'nik' => 'required|numeric',            
        'tempat_lahir' => 'required',            
        'tanggal_lahir' => 'required',
        'idagama' => 'required|numeric|exists:agama,idagama',
        'id_kebutuhan_khusus' => 'required|numeric|exists:kebutuhan_khusus,id_kebutuhan',

        'address1_provinsi_id' => 'required',
        'address1_provinsi' => 'required',
        'address1_kabupaten_id' => 'required',
        'address1_kabupaten' => 'required',
        'address1_kecamatan_id' => 'required',
        'address1_kecamatan' => 'required',
        'address1_desa_id' => 'required',
        'address1_kelurahan' => 'required',
        'alamat_tempat_tinggal' => 'required',
        'address1_rt' => 'required',
        'address1_rw' => 'required',
        'kode_pos' => 'required|max:5',
        'kewarganegaraan' => 'required|numeric|exists:negara,id',
        
        'anak_ke' => 'required',
        'jumlah_saudara' => 'required',
        'golongan_darah' => 'required',                
        
        'tinggi' => 'required',                
        'berat_badan' => 'required',                
        'ukuran_seragam' => 'required',                
        'id_moda' => 'required|numeric|exists:moda_transportasi,id_moda',                
        'jarak_ke_sekolah' => 'required',                
        'waktu_tempuh' => 'required',                
        'saudara_mendaftar_tidak' => 'required|in:0,1',
        'kode_jenjang' => 'required',
      ]);

      //cek usia
      $this->checkUsia($request);

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
        
        if($request->input('saudara_mendaftar_tidak') == '0')
        {
          $saudara_mendaftar = $request->input('saudara_mendaftar');
          foreach($saudara_mendaftar as $v)
          {
            switch($v)
            {
              case 1:
                $formulir->sibling_tk=$request->input('sibling_tk');                
              break;
              case 2:
                $formulir->sibling_sd=$request->input('sibling_sd');                
              break;
              case 3:
                $formulir->sibling_smp=$request->input('sibling_smp');                
              break;
              case 4:
                $formulir->sibling_sma=$request->input('sibling_sma');
              break;
            }
          }          
        }
        else
        {
          $formulir->sibling_tk=null;                
          $formulir->sibling_sd=null;                
          $formulir->sibling_smp=null;                
          $formulir->sibling_sma=null;                
        }
        $formulir->kode_jenjang=$request->input('kode_jenjang');

        $formulir->save();

        $user=$formulir->User;
        $user->name = strtoupper($request->input('nama_siswa'));    
        $user->save();    

        return $formulir;
      });
      return Response()->json([
        'status' => 1,
        'pid' => 'store',
        'formulir' => $formulir,          
        'message' => 'Formulir Pendaftaran Peserta Didik baru berhasil diubah.'
      ], 200);
    }
    catch (Exception $e)
    {
      return Response()->json([
        'status' => 1,
        'pid' => 'update',                
        'message' => [$e->getMessage()]
      ], 422);
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
        'status' => 1,
        'pid' => 'update',                
        'message' => ["Formulir Situasi Keluarga dengan ID ($id) gagal diperoleh"]
      ], 422);
    }
    else
    {       
      $this->validate($request, [
        'tinggal_bersama' => 'required',            
        'status_pernikahan' => 'required',
      ]);

      $data_siswa = \DB::transaction(function () use ($request, $formulir){                            
        $formulir->tinggal_bersama=strtoupper($request->input('tinggal_bersama'));
        $formulir->status_pernikahan=$request->input('status_pernikahan');    
        $formulir->desc=$request->input('desc');     
        $formulir->save();
        return $formulir;
      });
      return Response()->json([
        'status' => 1,
        'pid' => 'update',
        'formulir' => $formulir,          
        'message' => 'Formulir Situasi Keluarga baru berhasil diubah.'
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
        'status' => 1,
        'pid' => 'update',                
        'message' => ["Formulir Biodata Ayah dengan ID ($id) gagal diperoleh"]
      ], 422);
    }
    else
    {       
      $this->validate($request, [
        'nama_ayah' => 'required',            
        'hubungan' => 'required',                            
        'tempat_lahir' => 'required',            
        'tanggal_lahir' => 'required',
        'idagama' => 'required|numeric|exists:agama,idagama',

        'address1_provinsi_id' => 'required',
        'address1_provinsi' => 'required',
        'address1_kabupaten_id' => 'required',
        'address1_kabupaten' => 'required',
        'address1_kecamatan_id' => 'required',
        'address1_kecamatan' => 'required',
        'address1_desa_id' => 'required',
        'address1_kelurahan' => 'required',
        'alamat_tempat_tinggal' => 'required',                
        'kewarganegaraan' => 'required|numeric|exists:negara,id',
        
        'nomor_hp' => 'required',
        'email' => 'required',
        'pendidikan' => 'required',
        'pekerjaan_instansi' => 'required',        
        'penghasilan_bulanan' => 'required|numeric', 
        'fb_account' => 'required', 
        'ig_account' => 'required', 
        'tiktok_account' => 'required', 
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
        $formulir->fb_account=$request->input('fb_account');
        $formulir->ig_account=$request->input('ig_account');
        $formulir->tiktok_account=$request->input('tiktok_account');
        
        $formulir->save();

        $user=$formulir->User;
        $user->nomor_hp = $request->input('nomor_hp');    
        $user->email = $request->input('email');    
        $user->save();    

        return $formulir;
      });
      return Response()->json([
        'status' => 1,
        'pid' => 'update',
        'formulir' => $formulir,          
        'message' => 'Formulir Biodata Ayah baru berhasil diubah.'
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
        'status' => 1,
        'pid' => 'update',                
        'message' => ["Formulir Biodata Ibu dengan ID ($id) gagal diperoleh"]
      ], 422);
    }
    else
    {       
      $this->validate($request, [
        'nama_ibu' => 'required',            
        'hubungan' => 'required',                            
        'tempat_lahir' => 'required',            
        'tanggal_lahir' => 'required',
        'idagama' => 'required|numeric|exists:agama,idagama',

        'address1_provinsi_id' => 'required',
        'address1_provinsi' => 'required',
        'address1_kabupaten_id' => 'required',
        'address1_kabupaten' => 'required',
        'address1_kecamatan_id' => 'required',
        'address1_kecamatan' => 'required',
        'address1_desa_id' => 'required',
        'address1_kelurahan' => 'required',
        'alamat_tempat_tinggal' => 'required',                
        'kewarganegaraan' => 'required|numeric|exists:negara,id',
        
        'nomor_hp' => 'required',
        'email' => 'required',
        'pendidikan' => 'required',
        'pekerjaan_instansi' => 'required',        
        'penghasilan_bulanan' => 'required|numeric', 
        'fb_account' => 'required', 
        'ig_account' => 'required', 
        'tiktok_account' => 'required', 
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
        $formulir->fb_account=$request->input('fb_account');
        $formulir->ig_account=$request->input('ig_account');
        $formulir->tiktok_account=$request->input('tiktok_account');
        
        $formulir->save(); 

        return $formulir;
      });
      return Response()->json([
        'status' => 1,
        'pid' => 'update',
        'formulir' => $formulir,          
        'message' => 'Formulir Biodata Ibu baru berhasil diubah.'
      ], 200);
    }
  }   
  /**
   * update formulir pendaftaran
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function updatebiodatawali(Request $request,$id)
  {
    $formulir=FormulirPendaftaranFModel::find($id);

    if (is_null($formulir))
    {
      return Response()->json([
        'status' => 1,
        'pid' => 'update',                
        'message' => ["Formulir Biodata Wali dengan ID ($id) gagal diperoleh"]
      ], 422);
    }
    else
    {       
      $this->validate($request, [
        'nama_wali' => 'required',                                
        'tempat_lahir' => 'required',            
        'tanggal_lahir' => 'required',
        'idagama' => 'required|numeric|exists:agama,idagama',

        'address1_provinsi_id' => 'required',
        'address1_provinsi' => 'required',
        'address1_kabupaten_id' => 'required',
        'address1_kabupaten' => 'required',
        'address1_kecamatan_id' => 'required',
        'address1_kecamatan' => 'required',
        'address1_desa_id' => 'required',
        'address1_kelurahan' => 'required',
        'alamat_tempat_tinggal' => 'required',                
        'kewarganegaraan' => 'required|numeric|exists:negara,id',
        
        'nomor_hp' => 'required',
        'email' => 'required',
        'pendidikan' => 'required',
        'pekerjaan_instansi' => 'required',        
        'penghasilan_bulanan' => 'required|numeric', 
        'fb_account' => 'required', 
        'ig_account' => 'required', 
        'tiktok_account' => 'required', 
      ]);

      $data_siswa = \DB::transaction(function () use ($request,$formulir){                            
        $formulir->nama_wali=strtoupper($request->input('nama_wali'));        
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
        $formulir->fb_account=$request->input('fb_account');
        $formulir->ig_account=$request->input('ig_account');
        $formulir->tiktok_account=$request->input('tiktok_account');

        $formulir->save(); 

        return $formulir;
      });
      return Response()->json([
        'status' => 1,
        'pid' => 'update',
        'formulir' => $formulir,          
        'message' => 'Formulir Biodata Wali berhasil diubah.'
      ], 200);
    }
  }  
  /**
   * update formulir pendaftaran
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */
  public function updatekontakdarurat(Request $request,$id)
  {
    $formulir=FormulirPendaftaranEModel::find($id);

    if (is_null($formulir))
    {
      return Response()->json([
        'status' => 1,
        'pid' => 'update',                
        'message' => ["Formulir Biodata Ibu dengan ID ($id) gagal diperoleh"]
      ], 422);
    }
    else
    {       
      $this->validate($request, [
        'nama_kontak' => 'required',            
        'hubungan' => 'required',                                
        'alamat_kontak' => 'required',                        
        'nomor_hp' => 'required',        
      ]);

      $data_siswa = \DB::transaction(function () use ($request,$formulir){                            
        $formulir->nama_kontak=strtoupper($request->input('nama_kontak'));
        $formulir->hubungan=$request->input('hubungan');            
        $formulir->alamat_kontak=$request->input('alamat_kontak');
        $formulir->nomor_hp=$request->input('nomor_hp');
        
        $formulir->save(); 

        return $formulir;
      });
      return Response()->json([
        'status' => 1,
        'pid' => 'update',
        'formulir' => $formulir,          
        'message' => 'Formulir Kontak Darurat baru berhasil diubah.'
      ], 200);
    }
  }           
  public function uploadfileselfi (Request $request,$id)
  {
    $formulir=PersyaratanPPDBModel::find($id);
    if (is_null($formulir))
    {
      return Response()->json([
        'status' => 1,
        'pid' => 'fetchdata',                
        'message' => ["Formulir Pendaftaran dengan ID ($id) gagal diperoleh"]
      ], 422);
    }
    else
    {
      $this->validate($request, [                      
        'filefotoselfi' => 'required'                        
      ]);
      $filefotoselfi = $request->file('filefotoselfi');
      $mime_type=$filefotoselfi->getMimeType();
      if ($mime_type=='application/pdf' || $mime_type=='image/png' || $mime_type=='image/jpeg')
      {
        $folder=HelperPendaftaran::public_path('persyaratanppdb/');
        $file_name=uniqid('fotoselfi_').".".$filefotoselfi->getClientOriginalExtension();
        if (is_file(HelperPendaftaran::public_path(str_replace('storage', '', $formulir->file_fotoselfi))))                
        {
          unlink(HelperPendaftaran::public_path(str_replace('storage', '', $formulir->file_fotoselfi)));
        }                
        $formulir->file_fotoselfi="persyaratanppdb/$file_name";
        $formulir->save();
        $filefotoselfi->move($folder,$file_name);
        return Response()->json([
          'status' => 1,
          'pid' => 'update',
          'formulir' => $formulir,                
          'message' => "Foto Selfi berhasil diupload"
        ], 200);   
          
      }
      else
      {
        return Response()->json([
          'status' => 0,
          'pid' => 'update',
          'message' => ["Extensi file yang diupload bukan jpg, png atau pdf."]
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
        'status' => 1,
        'pid' => 'fetchdata',                
        'message' => ["Formulir Pendaftaran dengan ID ($id) gagal diperoleh"]
      ], 422);
    }
    else
    {
      $this->validate($request, [                      
        'filektpayah' => 'required'                        
      ]);
      $filektpayah = $request->file('filektpayah');
      $mime_type=$filektpayah->getMimeType();
      if ($mime_type=='application/pdf' || $mime_type=='image/png' || $mime_type=='image/jpeg')
      {
        $folder=HelperPendaftaran::public_path('persyaratanppdb/');
        $file_name=uniqid('ktp_').".".$filektpayah->getClientOriginalExtension();
        if (is_file(HelperPendaftaran::public_path(str_replace('storage', '', $formulir->file_ktp_ayah))))                
        {
          unlink(HelperPendaftaran::public_path(str_replace('storage', '', $formulir->file_ktp_ayah)));
        }                
        $formulir->file_ktp_ayah="persyaratanppdb/$file_name";
        $formulir->save();
        $filektpayah->move($folder,$file_name);
        return Response()->json([
          'status' => 1,
          'pid' => 'store',       
          'formulir' => $formulir,                
          'message' => "File KTP Ayah Wali berhasil diupload"
        ], 200);   
          
      }
      else
      {
        return Response()->json([
          'status' => 0,
          'pid' => 'store',
          'message' => ["Extensi file yang diupload bukan jpg, png atau pdf."]
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
        'status' => 1,
        'pid' => 'fetchdata',                
        'message' => ["Formulir Pendaftaran dengan ID ($id) gagal diperoleh"]
      ], 422);
    }
    else
    {
      $this->validate($request, [                      
        'filektpibu' => 'required'                        
      ]);
      $filektpibu = $request->file('filektpibu');
      $mime_type=$filektpibu->getMimeType();
      if ($mime_type=='application/pdf' || $mime_type=='image/png' || $mime_type=='image/jpeg')
      {
        $folder=HelperPendaftaran::public_path('persyaratanppdb/');
        $file_name=uniqid('ktp_').".".$filektpibu->getClientOriginalExtension();
        if (is_file(HelperPendaftaran::public_path(str_replace('storage', '', $formulir->file_ktp_ibu))))                
        {
          unlink(HelperPendaftaran::public_path(str_replace('storage', '', $formulir->file_ktp_ibu)));
        }                
        $formulir->file_ktp_ibu="persyaratanppdb/$file_name";
        $formulir->save();
        $filektpibu->move($folder,$file_name);
        return Response()->json([
          'status' => 1,
          'pid' => 'store',       
          'formulir' => $formulir,                
          'message' => "File KTP Ibu Wali berhasil diupload"
        ], 200);             
      }
      else
      {
        return Response()->json([
          'status' => 0,
          'pid' => 'store',
          'message' => ["Extensi file yang diupload bukan jpg, png atau pdf."]
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
        'status' => 1,
        'pid' => 'fetchdata',                
        'message' => ["Formulir Pendaftaran dengan ID ($id) gagal diperoleh"]
      ], 422);
    }
    else
    {
      $this->validate($request, [                      
        'filekk' => 'required'                        
      ]);
      $filekk = $request->file('filekk');
      $mime_type=$filekk->getMimeType();
      if ($mime_type=='application/pdf' || $mime_type=='image/png' || $mime_type=='image/jpeg')
      {
        $folder=HelperPendaftaran::public_path('persyaratanppdb/');
        $file_name=uniqid('kk_').".".$filekk->getClientOriginalExtension();
        if (is_file(HelperPendaftaran::public_path(str_replace('storage', '', $formulir->file_kk))))                
        {
          unlink(HelperPendaftaran::public_path(str_replace('storage', '', $formulir->file_kk)));
        }                
        $formulir->file_kk="persyaratanppdb/$file_name";
        $formulir->save();
        $filekk->move($folder,$file_name);
        return Response()->json([
                      'status' => 1,
                      'pid' => 'store',       
                      'formulir' => $formulir,                
                      'message' => "File KK berhasil diupload"
                    ], 200);   
          
      }
      else
      {
        return Response()->json([
                    'status' => 0,
                    'pid' => 'store',
                    'message' => ["Extensi file yang diupload bukan jpg, png atau pdf."]
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
                  'status' => 1,
                  'pid' => 'fetchdata',                
                  'message' => ["Formulir Pendaftaran dengan ID ($id) gagal diperoleh"]
                ], 422);
    }
    else
    {
      $this->validate($request, [                      
        'fileaktalahir' => 'required'                        
      ]);
      $fileaktalahir = $request->file('fileaktalahir');
      $mime_type=$fileaktalahir->getMimeType();
      if ($mime_type=='application/pdf' || $mime_type=='image/png' || $mime_type=='image/jpeg')
      {
        $folder=HelperPendaftaran::public_path('persyaratanppdb/');
        $file_name=uniqid('aktalahir_').".".$fileaktalahir->getClientOriginalExtension();
        if (is_file(HelperPendaftaran::public_path(str_replace('storage', '', $formulir->file_aktalahir))))                
        {
          unlink(HelperPendaftaran::public_path(str_replace('storage', '', $formulir->file_aktalahir)));
        }                
        $formulir->file_aktalahir="persyaratanppdb/$file_name";
        $formulir->save();
        $fileaktalahir->move($folder,$file_name);
        return Response()->json([
          'status' => 1,
          'pid' => 'store',       
          'formulir' => $formulir,                
          'message' => "File KK berhasil diupload"
        ], 200);   
      }
      else
      {
        return Response()->json([
          'status' => 0,
          'pid' => 'store',
          'message' => ["Extensi file yang diupload bukan jpg, png atau pdf."]
        ], 422);
      }            
    }
  }
  public function uploadfilescreenshoot (Request $request,$id)
  {
    $formulir=PersyaratanPPDBModel::find($id);
    if (is_null($formulir))
    {
      return Response()->json([
        'status' => 1,
        'pid' => 'fetchdata',                
        'message' => ["Formulir Pendaftaran dengan ID ($id) gagal diperoleh"]
      ], 422);
    }
    else
    {
      $this->validate($request, [                      
        'filescreenshoot' => 'required'                        
      ]);
      $filescreenshoot = $request->file('filescreenshoot');
      $mime_type=$filescreenshoot->getMimeType();
      if ($mime_type=='application/pdf' || $mime_type=='image/png' || $mime_type=='image/jpeg')
      {
        $folder=HelperPendaftaran::public_path('persyaratanppdb/');
        $file_name=uniqid('screenshoot_').".".$filescreenshoot->getClientOriginalExtension();
        if (is_file(HelperPendaftaran::public_path(str_replace('storage', '', $formulir->file_screenshoot_medsos))))                
        {
          unlink(HelperPendaftaran::public_path(str_replace('storage', '', $formulir->file_screenshoot_medsos)));
        }                
        $formulir->file_screenshoot_medsos="persyaratanppdb/$file_name";
        $formulir->save();
        $filescreenshoot->move($folder,$file_name);
        return Response()->json([
          'status' => 1,
          'pid' => 'store',       
          'formulir' => $formulir,                
          'message' => "File Screenshoot berhasil diupload"
        ], 200); 
      }
      else
      {
        return Response()->json([
          'status' => 0,
          'pid' => 'store',
          'message' => ["Extensi file yang diupload bukan jpg, png atau pdf."]
        ], 422);
      }            
    }
  }
  public function uploadfilesertifikat (Request $request,$id)
  {
    $formulir=PersyaratanPPDBModel::find($id);
    if (is_null($formulir))
    {
      return Response()->json([
        'status' => 1,
        'pid' => 'fetchdata',                
        'message' => ["Formulir Pendaftaran dengan ID ($id) gagal diperoleh"]
      ], 422);
    }
    else
    {
      $this->validate($request, [                      
        'filesertifikat' => 'required'                        
      ]);
      $filesertifikat = $request->file('filesertifikat');
      $mime_type=$filesertifikat->getMimeType();
      if ($mime_type=='application/pdf' || $mime_type=='image/png' || $mime_type=='image/jpeg')
      {
        $folder=HelperPendaftaran::public_path('persyaratanppdb/');
        $file_name=uniqid('sertifikat_').".".$filesertifikat->getClientOriginalExtension();
        if (is_file(HelperPendaftaran::public_path(str_replace('storage', '', $formulir->file_sertifikat))))                
        {
          unlink(HelperPendaftaran::public_path(str_replace('storage', '', $formulir->file_sertifikat)));
        }                
        $formulir->file_sertifikat="persyaratanppdb/$file_name";
        $formulir->save();
        $filesertifikat->move($folder,$file_name);
        return Response()->json([
          'status' => 1,
          'pid' => 'store',       
          'formulir' => $formulir,                
          'message' => "File sertifikat berhasil diupload"
        ], 200); 
      }
      else
      {
        return Response()->json([
          'status' => 0,
          'pid' => 'store',
          'message' => ["Extensi file yang diupload bukan jpg, png atau pdf."]
        ], 422);
      }            
    }
  }
  public function uploadfilenisn (Request $request,$id)
  {
    $formulir=PersyaratanPPDBModel::find($id);
    if (is_null($formulir))
    {
      return Response()->json([
        'status' => 1,
        'pid' => 'fetchdata',                
        'message' => ["Formulir Pendaftaran dengan ID ($id) gagal diperoleh"]
      ], 422);
    }
    else
    {
      $this->validate($request, [                      
        'filenisn' => 'required'                        
      ]);
      $filenisn = $request->file('filenisn');
      $mime_type=$filenisn->getMimeType();
      if ($mime_type=='application/pdf' || $mime_type=='image/png' || $mime_type=='image/jpeg')
      {
        $folder=HelperPendaftaran::public_path('persyaratanppdb/');
        $file_name=uniqid('nisn_').".".$filenisn->getClientOriginalExtension();
        if (is_file(HelperPendaftaran::public_path(str_replace('storage', '', $formulir->file_nisn))))                
        {
          unlink(HelperPendaftaran::public_path(str_replace('storage', '', $formulir->file_nisn)));
        }                
        $formulir->file_nisn="persyaratanppdb/$file_name";
        $formulir->save();
        $filenisn->move($folder,$file_name);
        return Response()->json([
          'status' => 1,
          'pid' => 'store',       
          'formulir' => $formulir,                
          'message' => "File nisn berhasil diupload"
        ], 200); 
      }
      else
      {
        return Response()->json([
          'status' => 0,
          'pid' => 'store',
          'message' => ["Extensi file yang diupload bukan jpg, png atau pdf."]
        ], 422);
      }            
    }
  }
  public function uploadfilekia (Request $request,$id)
  {
    $formulir=PersyaratanPPDBModel::find($id);
    if (is_null($formulir))
    {
      return Response()->json([
        'status' => 1,
        'pid' => 'fetchdata',                
        'message' => ["Formulir Pendaftaran dengan ID ($id) gagal diperoleh"]
      ], 422);
    }
    else
    {
      $this->validate($request, [                      
        'filekia' => 'required'                        
      ]);
      $filekia = $request->file('filekia');
      $mime_type=$filekia->getMimeType();
      if ($mime_type=='application/pdf' || $mime_type=='image/png' || $mime_type=='image/jpeg')
      {
        $folder=HelperPendaftaran::public_path('persyaratanppdb/');
        $file_name=uniqid('kia_').".".$filekia->getClientOriginalExtension();
        if (is_file(HelperPendaftaran::public_path(str_replace('storage', '', $formulir->file_kia))))                
        {
          unlink(HelperPendaftaran::public_path(str_replace('storage', '', $formulir->file_kia)));
        }                
        $formulir->file_kia="persyaratanppdb/$file_name";
        $formulir->save();
        $filekia->move($folder,$file_name);
        return Response()->json([
          'status' => 1,
          'pid' => 'store',       
          'formulir' => $formulir,                
          'message' => "File kia berhasil diupload"
        ], 200); 
      }
      else
      {
        return Response()->json([
          'status' => 0,
          'pid' => 'store',
          'message' => ["Extensi file yang diupload bukan jpg, png atau pdf."]
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

    $user = User::where('isdeleted', '1')
    ->find($id); 
    
    if (is_null($user))
    {
      return Response()->json([
        'status' => 1,
        'pid' => 'destroy',                
        'message' => ["Calon Peserta Didik Baru dengan ID ($id) gagal dihapus"]
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
        'status' => 1,
        'pid' => 'destroy',                
        'message' => "Peserta Didik Baru ($name) berhasil dihapus"
      ], 200);
    }
          
  }      
}