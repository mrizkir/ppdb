<?php

namespace App\Models\DMaster;

use Illuminate\Database\Eloquent\Model;

class KebutuhanKhususModel extends Model {    
     /**
     * nama tabel model ini.
     *
     * @var string
     */
    protected $table = 'kebutuhan_khusus';
    /**
     * primary key tabel ini.
     *
     * @var string
     */
    protected $primaryKey = 'id_kebutuhan';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_kebutuhan', 'nama_kebutuhan'
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
    public $timestamps = false;
}