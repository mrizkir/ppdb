<?php

namespace App\Models\Keuangan;

use Illuminate\Database\Eloquent\Model;

class KonfirmasiPembayaranModel extends Model {    
     /**
     * nama tabel model ini.
     *
     * @var string
     */
    protected $table = 'konfirmasi_pembayaran';
    /**
     * primary key tabel ini.
     *
     * @var string
     */
    protected $primaryKey = 'transaksi_id';
    protected $keyType = 'string';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [        
        'transaksi_id',                 
        'user_id',
        'formulir_id',
        'ta',
        'kode_jenjang',
        'no_transaksi',        
        'id_channel',        
        'total_bayar',
        'nomor_rekening_pengirim',
        'nama_rekening_pengirim',
        'nama_bank_pengirim',
        'desc',
        'tanggal_bayar',
        'bukti_bayar',        
        'verified',        
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

    public function user()
    {
        return $this->belongsTo('App\Models\User','user_id','id');
    }
    public function formulir()
    {
        return $this->belongsTo('App\Models\SPSB\FormulirPendaftaranAModel','formulir_id','id');
    }

    /**
     * Simpan bukti bayar. Jika ada formulir_id, buat transaksi baru
     * tanpa menimpa bukti jenjang sebelumnya.
     */
    public static function simpanBukti($userId, array $values, $formulirId = null)
    {
        $formulirId = $formulirId ? (string) $formulirId : null;
        if ($formulirId) {
            $row = static::where('user_id', $userId)
                ->where('formulir_id', $formulirId)
                ->first();
            if ($row) {
                $row->fill($values);
                $row->save();
                return $row;
            }

            $row = new static();
            $row->exists = false;
            $row->forceFill(array_merge($values, [
                'transaksi_id' => \Ramsey\Uuid\Uuid::uuid4()->toString(),
                'user_id' => $userId,
                'formulir_id' => $formulirId,
            ]));
            $row->save();
            return $row;
        }

        return static::updateOrCreate(
            [
                'user_id' => $userId,
                'transaksi_id' => $userId,
            ],
            $values
        );
    }
}