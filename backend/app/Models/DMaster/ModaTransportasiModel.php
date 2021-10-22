<?php

namespace App\Models\DMaster;

use Illuminate\Database\Eloquent\Model;

class ModaTransportasiModel extends Model {    
     /**
     * nama tabel model ini.
     *
     * @var string
     */
    protected $table = 'moda_transportasi';
    /**
     * primary key tabel ini.
     *
     * @var string
     */
    protected $primaryKey = 'id_moda';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_moda', 'nama_moda'
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