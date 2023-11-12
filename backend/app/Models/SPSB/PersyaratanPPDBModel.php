<?php

namespace App\Models\SPSB;

use Illuminate\Database\Eloquent\Model;

class PersyaratanPPDBModel extends Model {    
	 /**
	 * nama tabel model ini.
	 *
	 * @var string
	 */
	protected $table = 'persyaratan_ppdb';
	/**
	 * primary key tabel ini.
	 *
	 * @var string
	 */
	protected $primaryKey = 'user_id';
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [
		'user_id',
		'file_fotoselfi',
		'file_ktp_ayah',
		'file_ktp_ibu',
		'file_kk',
		'file_aktalahir',
		'file_screenshoot_medsos',
	];
	/**
	 * enable auto_increment.
	 *
	 * @var string
	 */
	public $incrementing = false;
	/**
	 * activated timestamps.
	 *
	 * @var string
	 */
	public $timestamps = true;
}