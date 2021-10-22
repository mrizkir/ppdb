<?php

namespace App\Models\DMaster;

use Illuminate\Database\Eloquent\Model;

class AgamaModel extends Model {    
     /**
     * nama tabel model ini.
     *
     * @var string
     */
    protected $table = 'agama';
    /**
     * primary key tabel ini.
     *
     * @var string
     */
    protected $primaryKey = 'idagama';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'idagama', 'nama_agama'
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