<?php
$router->get('/', function () use ($router) {
	return 'PPDB API';
});

$router->group(['prefix'=>'v3'], function () use ($router)
{
	//data master - negara
	$router->get('/datamaster/negara',['uses'=>'DMaster\NegaraController@index','as'=>'negara.index']);
	
	//dmaster - provinsi
	$router->get('/datamaster/provinsi',['uses'=>'DMaster\ProvinsiController@index','as'=>'provinsi.index']);
	$router->get('/datamaster/provinsi/{id}/kabupaten',['uses'=>'DMaster\ProvinsiController@kabupaten','as'=>'provinsi.kabupaten']);

	//dmaster - kabupaten
	$router->get('/datamaster/kabupaten',['uses'=>'DMaster\KabupatenController@index','as'=>'kabupaten.index']);
	$router->get('/datamaster/kabupaten/{id}/kecamatan',['uses'=>'DMaster\KabupatenController@kecamatan','as'=>'kabupaten.kecamatan']);

	//dmaster - kecamatan
	$router->get('/datamaster/kecamatan',['uses'=>'DMaster\KecamatanController@index','as'=>'kecamatan.index']);
	$router->get('/datamaster/kecamatan/{id}/desa',['uses'=>'DMaster\KecamatanController@desa','as'=>'kecamatan.desa']);

	//dmaster - tahun ajaran
	$router->get('/datamaster/tahunajaran',['uses'=>'DMaster\TahunAjaranController@index','as'=>'tahunajaran.index']);
	$router->get('/datamaster/tahunajaran/{id}/daftarbulan',['uses'=>'DMaster\TahunAjaranController@daftarbulan','as'=>'tahunajaran.daftarbulan']);

	//data master - moda transportasi
	$router->get('/datamaster/modatransportasi',['uses'=>'DMaster\ModaTransportasiController@index','as'=>'modatransportasi.index']);
	
	//data master - agama
	$router->get('/datamaster/agama',['uses'=>'DMaster\AgamaController@index','as'=>'agama.index']);
	
	//data master - kebutuhan khusus
	$router->get('/datamaster/kebutuhankhusus',['uses'=>'DMaster\KebutuhanKhususController@index','as'=>'kebutuhankhusus.index']);
	
	//data master - jenjang studi
	$router->get('/datamaster/jenjangstudi',['uses'=>'DMaster\JenjangStudiController@index','as'=>'jenjangstudi.index']);

	//pendaftaran siswa baru
	$router->post('/spsb/psb/store',['uses'=>'SPSB\PSBController@store','as'=>'psb.store']);
	$router->post('/spsb/psb/konfirmasi',['uses'=>'SPSB\PSBController@konfirmasi','as'=>'psb.konfirmasi']);
	$router->post('/spsb/psb/konfirmasipembayaran',['uses'=>'SPSB\PSBController@konfirmasipembayaran','as'=>'psb.konfirmasipembayaran']);

	//untuk uifront
	$router->get('/system/setting/uifront',['uses'=>'System\UIController@frontend','as'=>'uifront.frontend']);

	//auth login
	$router->post('/auth/login',['uses'=>'AuthController@login','as'=>'auth.login']);
});

$router->group(['prefix'=>'v3','middleware'=>'auth:api'], function () use ($router)
{
	//authentication
	$router->post('/auth/logout',['uses'=>'AuthController@logout','as'=>'auth.logout']);
	$router->get('/auth/refresh',['uses'=>'AuthController@refresh','as'=>'auth.refresh']);
	$router->get('/auth/me',['uses'=>'AuthController@me','as'=>'auth.me']);

	// dashboard
	$router->post('/dashboard/psb',['middleware'=>['role:superadmin|psb|keuangan'],'uses'=>'SPSB\SPSBController@index','as'=>'dashboardspsb.index']);    
	$router->post('/dashboard/keuangan',['middleware'=>['role:superadmin|psb|keuangan'],'uses'=>'Keuangan\KeuanganController@index','as'=>'dashboardkeuangan.index']);

	//data master - tahun ajaran
	$router->post('/datamaster/tahunajaran/store',['middleware'=>['role:superadmin'],'uses'=>'DMaster\TahunAjaranController@store','as'=>'tahunajaran.store']);
	$router->put('/datamaster/tahunajaran/{id}',['middleware'=>['role:superadmin'],'uses'=>'DMaster\TahunAjaranController@update','as'=>'tahunajaran.update']);
	$router->delete('/datamaster/tahunajaran/{id}',['middleware'=>['role:superadmin'],'uses'=>'DMaster\TahunAjaranController@destroy','as'=>'`tahunajaran.destroy']);

	//data master - jenjang studi	
	$router->put('/datamaster/jenjangstudi/{id}',['uses'=>'DMaster\JenjangStudiController@update','as'=>'jenjangstudi.update']);

	//data master - kuota pendaftaran
	$router->get('/datamaster/kuotapendaftaran',['middleware'=>['role:superadmin'],'uses'=>'DMaster\KuotaPendaftaranController@index','as'=>'kuota-pendaftaran.index']);
	$router->post('/datamaster/kuotapendaftaran',['middleware'=>['role:superadmin'],'uses'=>'DMaster\KuotaPendaftaranController@store','as'=>'kuota-pendaftaran.store']);
	$router->put('/datamaster/kuotapendaftaran/{id}',['middleware'=>['role:superadmin'],'uses'=>'DMaster\KuotaPendaftaranController@update','as'=>'kuota-pendaftaran.update']);
	$router->delete('/datamaster/kuotapendaftaran/{id}',['middleware'=>['role:superadmin'],'uses'=>'DMaster\KuotaPendaftaranController@destroy','as'=>'`kuota-pendaftaran.destroy']);

	//spsb - pendaftaran siswa baru
	$router->post('/spsb/psb',['middleware'=>['role:superadmin|psb|keuangan'],'uses'=>'SPSB\PSBController@index','as'=>'psb.index']);
	$router->post('/spsb/psb/storependaftar',['middleware'=>['role:superadmin|psb'],'uses'=>'SPSB\PSBController@storependaftar','as'=>'psb.storependaftar']);
	$router->post('/spsb/psb/resend',['middleware'=>['role:superadmin|psb|siswabaru'],'uses'=>'SPSB\PSBController@resend','as'=>'psb.resend']);
	$router->put('/spsb/psb/updatependaftar/{id}',['middleware'=>['role:superadmin|psb'],'uses'=>'SPSB\PSBController@updatependaftar','as'=>'psb.updatependaftar']);
	$router->delete('/spsb/psb/{id}',['middleware'=>['role:superadmin|psb'],'uses'=>'SPSB\PSBController@destroy','as'=>'psb.destroy']);

	//spsb - formulir pendaftaran
	$router->post('/spsb/formulirpendaftaran',['middleware'=>['role:superadmin|psb|keuangan'],'uses'=>'SPSB\PSBController@formulirpendaftaran','as'=>'formulirpendaftaran.index']);
	$router->post('/spsb/situasikeluarga',['middleware'=>['role:superadmin|psb|keuangan'],'uses'=>'SPSB\PSBController@situasikeluarga','as'=>'formulirpendaftaran.situasikeluarga']);
	$router->post('/spsb/biodataayah',['middleware'=>['role:superadmin|psb|keuangan'],'uses'=>'SPSB\PSBController@biodataayah','as'=>'formulirpendaftaran.biodataayah']);
	$router->post('/spsb/biodataibu',['middleware'=>['role:superadmin|psb|keuangan'],'uses'=>'SPSB\PSBController@biodataibu','as'=>'formulirpendaftaran.biodataibu']);
	$router->get('/spsb/formulirpendaftaran/{id}',['middleware'=>['role:superadmin|psb|siswabaru'],'uses'=>'SPSB\PSBController@show','as'=>'formulirpendaftaran.show']);
	$router->get('/spsb/formulirpendaftaran/situasikeluarga/{id}',['middleware'=>['role:superadmin|psb|siswabaru'],'uses'=>'SPSB\PSBController@showsituasikeluarga','as'=>'formulirpendaftaran.showsituasikeluarga']);
	$router->get('/spsb/formulirpendaftaran/biodataayah/{id}',['middleware'=>['role:superadmin|psb|siswabaru'],'uses'=>'SPSB\PSBController@showbiodataayah','as'=>'formulirpendaftaran.showbiodataayah']);
	$router->get('/spsb/formulirpendaftaran/biodataibu/{id}',['middleware'=>['role:superadmin|psb|siswabaru'],'uses'=>'SPSB\PSBController@showbiodataibu','as'=>'formulirpendaftaran.showbiodataibu']);
	$router->get('/spsb/formulirpendaftaran/biodatawali/{id}',['middleware'=>['role:superadmin|psb|siswabaru'],'uses'=>'SPSB\PSBController@showbiodatawali','as'=>'formulirpendaftaran.showbiodatawali']);
	$router->get('/spsb/formulirpendaftaran/kontakdarurat/{id}',['middleware'=>['role:superadmin|psb|siswabaru'],'uses'=>'SPSB\PSBController@showkontakdarurat','as'=>'formulirpendaftaran.showkontakdarurat']);
	$router->put('/spsb/formulirpendaftaran/{id}',['middleware'=>['role:superadmin|psb|siswabaru'],'uses'=>'SPSB\PSBController@update','as'=>'formulirpendaftaran.update']);
	$router->put('/spsb/formulirpendaftaran/situasikeluarga/{id}',['middleware'=>['role:superadmin|psb|siswabaru'],'uses'=>'SPSB\PSBController@updatesituasikeluarga','as'=>'formulirpendaftaran.updatesituasikeluarga']);
	$router->put('/spsb/formulirpendaftaran/biodataayah/{id}',['middleware'=>['role:superadmin|psb|siswabaru'],'uses'=>'SPSB\PSBController@updatebiodataayah','as'=>'formulirpendaftaran.updatebiodataayah']);
	$router->put('/spsb/formulirpendaftaran/biodataibu/{id}',['middleware'=>['role:superadmin|psb|siswabaru'],'uses'=>'SPSB\PSBController@updatebiodataibu','as'=>'formulirpendaftaran.updatebiodataibu']);
	$router->put('/spsb/formulirpendaftaran/biodatawali/{id}',['middleware'=>['role:superadmin|psb|siswabaru'],'uses'=>'SPSB\PSBController@updatebiodatawali','as'=>'formulirpendaftaran.updatebiodatawali']);
	$router->put('/spsb/formulirpendaftaran/kontakdarurat/{id}',['middleware'=>['role:superadmin|psb|siswabaru'],'uses'=>'SPSB\PSBController@updatekontakdarurat','as'=>'formulirpendaftaran.updatekontakdarurat']);
	
	// id disini user_id
	$router->get('/spsb/formulirpendaftaran/persyaratanppdb/{id}',['middleware'=>['role:superadmin|psb|siswabaru'],'uses'=>'SPSB\PSBController@showpersyaratanppdb','as'=>'formulirpendaftaran.showpersyaratanppdb']);
	$router->post('/spsb/formulirpendaftaran/uploadfileselfi/{id}',['middleware'=>['role:superadmin|psb|siswabaru'],'uses'=>'SPSB\PSBController@uploadfileselfi','as'=>'formulirpendaftaran.uploadfileselfi']);
	$router->post('/spsb/formulirpendaftaran/uploadfilektpayah/{id}',['middleware'=>['role:superadmin|psb|siswabaru'],'uses'=>'SPSB\PSBController@uploadfilektpayah','as'=>'formulirpendaftaran.uploadfilektpayah']);
	$router->post('/spsb/formulirpendaftaran/uploadfilektpibu/{id}',['middleware'=>['role:superadmin|psb|siswabaru'],'uses'=>'SPSB\PSBController@uploadfilektpibu','as'=>'formulirpendaftaran.uploadfilektpibu']);
	$router->post('/spsb/formulirpendaftaran/uploadfilekk/{id}',['middleware'=>['role:superadmin|psb|siswabaru'],'uses'=>'SPSB\PSBController@uploadfilekk','as'=>'formulirpendaftaran.uploadfilekk']);
	$router->post('/spsb/formulirpendaftaran/uploadfileaktalahir/{id}',['middleware'=>['role:superadmin|psb|siswabaru'],'uses'=>'SPSB\PSBController@uploadfileaktalahir','as'=>'formulirpendaftaran.uploadfileaktalahir']);
	$router->post('/spsb/formulirpendaftaran/uploadfilescreenshoot/{id}',['middleware'=>['role:superadmin|psb|siswabaru'],'uses'=>'SPSB\PSBController@uploadfilescreenshoot','as'=>'formulirpendaftaran.uploadfilescreenshoot']);

	//spsb - report calon peserta didik
	$router->get('/report/calonpesertadidik',['middleware'=>['role:superadmin|psb|siswabaru'],'uses'=>'SPSB\ReportCalonPesertaDidikController@index','as'=>'reportcalonpesertadidik.index']);
	$router->post('/report/calonpesertadidik/printpdf',['middleware'=>['role:superadmin|psb|siswabaru'],'uses'=>'SPSB\ReportCalonPesertaDidikController@printpdf','as'=>'reportcalonpesertadidik.printpdf']);

	//spsb - report jenjang
	$router->post('/spsb/reportspsbprodi/printtoexcel',['middleware'=>['role:superadmin|psb|keuangan'],'uses'=>'SPSB\ReportSPSBProdiController@printtoexcel','as'=>'reportspsbprodi.printtoexcel']);

	//spsb - report report kelulusan
	$router->post('/spsb/reportspsbkelulusan',['middleware'=>['role:superadmin|psb|keuangan'],'uses'=>'SPSB\ReportKelulusanController@index','as'=>'reportspsbkelulusan.index']);
	$router->post('/spsb/reportspsbkelulusan/printtoexcel',['middleware'=>['role:superadmin|psb|keuangan'],'uses'=>'SPSB\ReportKelulusanController@printtoexcel','as'=>'reportspsbkelulusan.printtoexcel']);

	//spsb - persyaratan
	$router->post('/spsb/psbpersyaratan',['middleware'=>['role:superadmin|psb|siswabaru|keuangan'],'uses'=>'SPSB\PSBPersyaratanController@index','as'=>'psbpersyaratan.index']);
	$router->get('/spsb/psbpersyaratan/{id}',['middleware'=>['role:superadmin|psb|siswabaru'],'uses'=>'SPSB\PSBPersyaratanController@show','as'=>'psbpersyaratan.show']);
	$router->post('/spsb/psbpersyaratan/upload/{id}',['middleware'=>['role:superadmin|psb|siswabaru'],'uses'=>'SPSB\PSBPersyaratanController@upload','as'=>'psbpersyaratan.upload']);
	$router->post('/spsb/psbpersyaratan/verifikasipersyaratan/{id}',['middleware'=>['role:superadmin|psb'],'uses'=>'SPSB\PSBPersyaratanController@verifikasipersyaratan','as'=>'psbpersyaratan.verifikasipersyaratan']);
	$router->delete('/spsb/psbpersyaratan/hapusfilepersyaratan/{id}',['middleware'=>['role:superadmin|psb|siswabaru'],'uses'=>'SPSB\PSBPersyaratanController@hapusfilepersyaratan','as'=>'psbpersyaratan.hapusfilepersyaratan']);

	//keuangan - biaya komponen periode
  $router->post('/keuangan/biayakomponenperiode',['middleware'=>['role:superadmin|keuangan|mahasiswa'],'uses'=>'Keuangan\BiayaKomponenPeriodeController@index','as'=>'biayakomponenperiode.index']);
  $router->post('/keuangan/biayakomponenperiode/loadkombiperiode',['middleware'=>['role:superadmin|keuangan'],'uses'=>'Keuangan\BiayaKomponenPeriodeController@loadkombiperiode','as'=>'biayakomponenperiode.loadkombiperiode']);
  $router->post('/keuangan/biayakomponenperiode/updatebiaya',['middleware'=>['role:superadmin|keuangan'],'uses'=>'Keuangan\BiayaKomponenPeriodeController@updatebiaya','as'=>'biayakomponenperiode.updatebiaya']);
	
	//keuangan - konfirmasi pembayaran	
	$router->get('/keuangan/konfirmasipembayaran/{id}',['middleware'=>['role:superadmin|keuangan|siswa|siswabaru'],'uses'=>'Keuangan\KonfirmasiPembayaranController@show','as'=>'konfirmasipembayaran.show']);
	$router->put('/keuangan/konfirmasipembayaran/{id}',['middleware'=>['role:superadmin|keuangan|siswa|siswabaru'],'uses'=>'Keuangan\KonfirmasiPembayaranController@update','as'=>'konfirmasipembayaran.update']);

	// akademik kesiswaan
	$router->post('/akademik/kemahasiswaan/updatestatus/{id}',['middleware'=>['role:superadmin|pmb'],'uses'=>'Akademik\KemahasiswaanController@updatestatus','as'=>'kemahasiswaan.updatestatus']);

	//setting - permissions
	$router->get('/system/setting/permissions',['middleware'=>['role:superadmin|akademik|psb'],'uses'=>'System\PermissionsController@index','as'=>'permissions.index']);
	$router->post('/system/setting/permissions/store',['middleware'=>['role:superadmin'],'uses'=>'System\PermissionsController@store','as'=>'permissions.store']);
	$router->delete('/system/setting/permissions/{id}',['middleware'=>['role:superadmin'],'uses'=>'System\PermissionsController@destroy','as'=>'permissions.destroy']);

	//setting - roles
	$router->get('/system/setting/roles',['middleware'=>['role:superadmin'],'uses'=>'System\RolesController@index','as'=>'roles.index']);
	$router->post('/system/setting/roles/store',['middleware'=>['role:superadmin'],'uses'=>'System\RolesController@store','as'=>'roles.store']);
	$router->post('/system/setting/roles/storerolepermissions',['middleware'=>['role:superadmin'],'uses'=>'System\RolesController@storerolepermissions','as'=>'roles.storerolepermissions']);
	$router->post('/system/setting/roles/revokerolepermissions',['middleware'=>['role:superadmin'],'uses'=>'System\RolesController@revokerolepermissions','as'=>'users.revokerolepermissions']);
	$router->put('/system/setting/roles/{id}',['middleware'=>['role:superadmin'],'uses'=>'System\RolesController@update','as'=>'roles.update']);
	$router->delete('/system/setting/roles/{id}',['middleware'=>['role:superadmin'],'uses'=>'System\RolesController@destroy','as'=>'roles.destroy']);
	$router->get('/system/setting/roles/{id}/permission',['middleware'=>['role:superadmin'],'uses'=>'System\RolesController@rolepermissions','as'=>'roles.permission']);

	//setting - variables
	$router->get('/system/setting/variables',['middleware'=>['role:superadmin'],'uses'=>'System\VariablesController@index','as'=>'variables.index']);
	$router->get('/system/setting/variables/{id}',['middleware'=>['role:superadmin'],'uses'=>'System\VariablesController@show','as'=>'variables.show']);
	$router->put('/system/setting/variables',['middleware'=>['role:superadmin'],'uses'=>'System\VariablesController@update','as'=>'variables.update']);
	$router->post('/system/setting/variables/clear',['middleware'=>['role:superadmin'],'uses'=>'System\VariablesController@clear','as'=>'variables.clear']);

	//setting - users
	$router->get('/system/users',['middleware'=>['role:superadmin'],'uses'=>'System\UsersController@index','as'=>'users.index']);
	$router->post('/system/users/store',['middleware'=>['role:superadmin'],'uses'=>'System\UsersController@store','as'=>'users.store']);
	$router->put('/system/users/updatepassword/{id}',['uses'=>'System\UsersController@updatepassword','as'=>'users.updatepassword']);
	$router->post('/system/users/uploadfoto/{id}',['uses'=>'System\UsersController@uploadfoto','as'=>'users.uploadfoto']);
	$router->post('/system/users/resetfoto/{id}',['uses'=>'System\UsersController@resetfoto','as'=>'users.resetfoto']);
	$router->post('/system/users/syncallpermissions',['middleware'=>['role:superadmin'],'uses'=>'System\UsersController@syncallpermissions','as'=>'users.syncallpermissions']);
	$router->post('/system/users/storeuserpermissions',['middleware'=>['role:superadmin'],'uses'=>'System\UsersController@storeuserpermissions','as'=>'users.storeuserpermissions']);
	$router->post('/system/users/revokeuserpermissions',['middleware'=>['role:superadmin'],'uses'=>'System\UsersController@revokeuserpermissions','as'=>'users.revokeuserpermissions']);
	$router->put('/system/users/{id}',['middleware'=>['role:superadmin'],'uses'=>'System\UsersController@update','as'=>'users.update']);
	$router->get('/system/users/{id}',['uses'=>'System\UsersController@show','as'=>'users.show']);
	$router->delete('/system/users/{id}',['middleware'=>['role:superadmin'],'uses'=>'System\UsersController@destroy','as'=>'users.destroy']);
	$router->get('/system/users/{id}/permission',['middleware'=>['role:superadmin|bapelitbang|opd'],'uses'=>'System\UsersController@userpermissions','as'=>'users.permission']);
	$router->get('/system/users/{id}/jenjang',['middleware'=>['role:superadmin'],'uses'=>'System\UsersController@usersprodi','as'=>'users.jenjang']);
	$router->get('/system/users/{id}/roles',['uses'=>'System\UsersController@roles','as'=>'users.roles']);

	//setting - users keuangan
	$router->get('/system/userskeuangan',['middleware'=>['role:superadmin|keuangan'],'uses'=>'System\UsersKeuanganController@index','as'=>'userskeuangan.index']);
	$router->post('/system/userskeuangan/store',['middleware'=>['role:superadmin|keuangan'],'uses'=>'System\UsersKeuanganController@store','as'=>'userskeuangan.store']);
	$router->put('/system/userskeuangan/{id}',['middleware'=>['role:superadmin|keuangan'],'uses'=>'System\UsersKeuanganController@update','as'=>'userskeuangan.update']);
	$router->delete('/system/userskeuangan/{id}',['middleware'=>['role:superadmin|keuangan'],'uses'=>'System\UsersKeuanganController@destroy','as'=>'userskeuangan.destroy']);

	//setting - users psb
	$router->get('/system/userspsb',['middleware'=>['role:superadmin|psb'],'uses'=>'System\UsersPSBController@index','as'=>'userspsb.index']);
	$router->post('/system/userspsb/store',['middleware'=>['role:superadmin|psb'],'uses'=>'System\UsersPSBController@store','as'=>'userspsb.store']);
	$router->put('/system/userspsb/{id}',['middleware'=>['role:superadmin|psb'],'uses'=>'System\UsersPSBController@update','as'=>'userspsb.update']);
	$router->delete('/system/userspsb/{id}',['middleware'=>['role:superadmin|psb'],'uses'=>'System\UsersPSBController@destroy','as'=>'userspsb.destroy']);

	//setting - users akademik
	$router->get('/system/usersakademik',['middleware'=>['role:superadmin|akademik'],'uses'=>'System\UsersAkademikController@index','as'=>'usersakademik.index']);
	$router->post('/system/usersakademik/store',['middleware'=>['role:superadmin|akademik'],'uses'=>'System\UsersAkademikController@store','as'=>'usersakademik.store']);
	$router->put('/system/usersakademik/{id}',['middleware'=>['role:superadmin|akademik'],'uses'=>'System\UsersAkademikController@update','as'=>'usersakademik.update']);
	$router->delete('/system/usersakademik/{id}',['middleware'=>['role:superadmin|akademik'],'uses'=>'System\UsersAkademikController@destroy','as'=>'usersakademik.destroy']);

	//setting - users jenjang studi
	$router->get('/system/usersprodi',['middleware'=>['role:superadmin|programstudi'],'uses'=>'System\UsersProdiController@index','as'=>'usersprodi.index']);
	$router->post('/system/usersprodi/store',['middleware'=>['role:superadmin|programstudi'],'uses'=>'System\UsersProdiController@store','as'=>'usersprodi.store']);
	$router->put('/system/usersprodi/{id}',['middleware'=>['role:superadmin|programstudi'],'uses'=>'System\UsersProdiController@update','as'=>'usersprodi.update']);
	$router->get('/system/usersprodi/{id}',['middleware'=>['role:superadmin|programstudi'],'uses'=>'System\UsersProdiController@show','as'=>'usersprodi.show']);
	$router->delete('/system/usersprodi/{id}',['middleware'=>['role:superadmin|programstudi'],'uses'=>'System\UsersProdiController@destroy','as'=>'usersprodi.destroy']);

	//setting - users dosen
	$router->get('/system/usersdosen',['middleware'=>['role:superadmin|akademik'],'uses'=>'System\UsersDosenController@index','as'=>'usersdosen.index']);    
	$router->post('/system/usersdosen/store',['middleware'=>['role:superadmin|akademik'],'uses'=>'System\UsersDosenController@store','as'=>'usersdosen.store']);
	$router->put('/system/usersdosen/{id}',['middleware'=>['role:superadmin|akademik'],'uses'=>'System\UsersDosenController@update','as'=>'usersdosen.update']);
	$router->delete('/system/usersdosen/{id}',['middleware'=>['role:superadmin|akademik'],'uses'=>'System\UsersDosenController@destroy','as'=>'usersdosen.destroy']);

	//system-migration
	$router->post('/system/migration',['middleware'=>['role:superadmin|akademik'],'uses'=>'System\SystemMigrationController@index','as'=>'systemmigration.index']);
	$router->post('/system/migration/store',['middleware'=>['role:superadmin|akademik'],'uses'=>'System\SystemMigrationController@store','as'=>'systemmigration.store']);

	//untuk ui admin
	$router->get('/system/setting/uiadmin',['uses'=>'System\UIController@admin','as'=>'ui.admin']);

});
