<?php

namespace App\Http\Controllers\Keuangan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Keuangan\TransaksiModel;
use App\Models\Keuangan\TransaksiDetailModel;
use App\Models\Keuangan\KonfirmasiPembayaranModel;
use App\Helpers\Helper;

class KonfirmasiPembayaranController extends Controller 
{
    public function index(Request $request)
    {
        $this->hasAnyPermission(['KEUANGAN-KONFIRMASI-PEMBAYARAN_BROWSE', 'SPSB-PSB_BROWSE']);

        $this->validate($request, [
            'TA' => 'required',
            'kode_jenjang' => 'required',
        ]);

        $ta = $request->input('TA');
        $kodeJenjang = $request->input('kode_jenjang');

        $query = KonfirmasiPembayaranModel::query()
            ->select(\DB::raw('
                konfirmasi_pembayaran.transaksi_id,
                konfirmasi_pembayaran.user_id,
                COALESCE(konfirmasi_pembayaran.formulir_id, f_by_user.id) AS formulir_id,
                users.name,
                users.username,
                users.email,
                users.nomor_hp,
                users.foto,
                COALESCE(f_by_id.nominal_transfer, f_by_user.nominal_transfer, users.code) AS code,
                COALESCE(konfirmasi_pembayaran.ta, f_by_id.ta, f_by_user.ta) AS ta,
                COALESCE(konfirmasi_pembayaran.kode_jenjang, f_by_id.kode_jenjang, f_by_user.kode_jenjang) AS kode_jenjang,
                konfirmasi_pembayaran.no_transaksi,
                CASE
                    WHEN konfirmasi_pembayaran.id_channel=1 THEN "TELLER BANK"
                    WHEN konfirmasi_pembayaran.id_channel=2 THEN "TRANSFER ATM"
                    WHEN konfirmasi_pembayaran.id_channel=3 THEN "INTERNET BANKING"
                    WHEN konfirmasi_pembayaran.id_channel=4 THEN "MOBILE BANKING"
                END AS nama_channel,
                konfirmasi_pembayaran.tanggal_bayar,
                konfirmasi_pembayaran.nomor_rekening_pengirim,
                konfirmasi_pembayaran.nama_rekening_pengirim,
                konfirmasi_pembayaran.nama_bank_pengirim,
                konfirmasi_pembayaran.total_bayar,
                konfirmasi_pembayaran.verified,
                CASE
                    WHEN konfirmasi_pembayaran.verified IS NULL THEN "N.A"
                    WHEN konfirmasi_pembayaran.verified=0 THEN "UNVERIFIED"
                    WHEN konfirmasi_pembayaran.verified=1 THEN "VERIFIED"
                END AS nama_status,
                konfirmasi_pembayaran.bukti_bayar,
                konfirmasi_pembayaran.created_at,
                konfirmasi_pembayaran.updated_at
            '))
            ->join('users', 'users.id', '=', 'konfirmasi_pembayaran.user_id')
            ->leftJoin('formulir_pendaftaran_a as f_by_id', 'f_by_id.id', '=', 'konfirmasi_pembayaran.formulir_id')
            ->leftJoin('formulir_pendaftaran_a as f_by_user', function ($join) use ($ta, $kodeJenjang) {
                $join->on('f_by_user.user_id', '=', 'konfirmasi_pembayaran.user_id')
                    ->where('f_by_user.ta', $ta)
                    ->where('f_by_user.kode_jenjang', $kodeJenjang);
            })
            ->where(function ($q) use ($ta, $kodeJenjang) {
                $q->where(function ($q2) use ($ta, $kodeJenjang) {
                    $q2->where('konfirmasi_pembayaran.ta', $ta)
                        ->where('konfirmasi_pembayaran.kode_jenjang', $kodeJenjang);
                })->orWhere(function ($q2) use ($ta, $kodeJenjang) {
                    $q2->where('f_by_id.ta', $ta)
                        ->where('f_by_id.kode_jenjang', $kodeJenjang);
                })->orWhere(function ($q2) {
                    $q2->whereNull('konfirmasi_pembayaran.ta')
                        ->whereNull('konfirmasi_pembayaran.formulir_id')
                        ->whereNotNull('f_by_user.id');
                });
            })
            ->orderBy('konfirmasi_pembayaran.created_at', 'desc');

        if ($request->filled('verified') && $request->input('verified') !== '' && $request->input('verified') !== 'all') {
            $query->where('konfirmasi_pembayaran.verified', (int) $request->input('verified'));
        }

        return Response()->json([
            'status' => 1,
            'pid' => 'fetchdata',
            'konfirmasi' => $query->get(),
            'message' => 'Fetch data konfirmasi pembayaran berhasil diperoleh',
        ], 200)->setEncodingOptions(JSON_NUMERIC_CHECK);
    }
    
    public function show(Request $request,$id)
    {
        $konfirmasi=$this->findKonfirmasi($request,$id, true);

        if (is_null($konfirmasi))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'fetchdata',                
                                    'message'=>["Fetch data transaksi dengan ID ($id) gagal diperoleh di KONFIRMASI PEMBAYARAN"]
                                ], 422); 
        }
        else
        {
            return Response()->json([
                                        'status'=>1,
                                        'pid'=>'fetchdata',  
                                        'konfirmasi'=>$konfirmasi,                                                                                                                                   
                                        'message'=>'Fetch data detail konfirmasi berhasil.'
                                    ], 200);     
        }
    }    
    /**
     * digunakan untuk merubah status transaksi menjadi paid
     */
    public function update(Request $request,$id)
    {
        $this->hasAnyPermission(['KEUANGAN-KONFIRMASI-PEMBAYARAN_UPDATE', 'SPSB-PSB_UPDATE']);

        $konfirmasi=$this->findKonfirmasi($request,$id, false);
        if (is_null($konfirmasi))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'update',                
                                    'message'=>["Update data transaksi dengan ID ($id) gagal diperoleh di KONFIRMASI PEMBAYARAN"]
                                ], 422); 
        }
        else
        {
            $this->validate($request, [                      
                'verified'=>'required'                        
            ]);
            $konfirmasi = \DB::transaction(function () use ($request,$konfirmasi){  
                $konfirmasi->verified=$request->input('verified');
                $konfirmasi->save();  
                return $konfirmasi;
            });
            
            return Response()->json([
                                        'status'=>1,
                                        'pid'=>'update',                                          
                                        'konfirmasi'=>$konfirmasi,                                          
                                        'message'=>"Mengubah data konfirmasi dengan id ($id) berhasil."                                        
                                    ], 200);   
        }
        
    }

    private function findKonfirmasi(Request $request, $id, $withSelect = false)
    {
        $query = $this->konfirmasiQuery($withSelect);
        $hasScope = $request->filled('formulir_id') || $request->filled('ta') || $request->filled('kode_jenjang');

        if ($hasScope) {
            $row = $this->findScopedKonfirmasi($query, $request, $id);
            if ($row) {
                return $row;
            }
        }

        $row = (clone $query)->find($id);
        if ($row) {
            return $row;
        }

        return (clone $query)->where('user_id', $id)->orderBy('created_at', 'desc')->first();
    }

    private function konfirmasiQuery($withSelect = false)
    {
        if (!$withSelect) {
            return KonfirmasiPembayaranModel::query();
        }

        return KonfirmasiPembayaranModel::select(\DB::raw('
                                                transaksi_id,
                                                user_id,
                                                formulir_id,
                                                ta,
                                                kode_jenjang,
                                                no_transaksi,
                                                CASE
                                                    WHEN id_channel=1 THEN "TELLER BANK"
                                                    WHEN id_channel=2 THEN "TRANSFER ATM"
                                                    WHEN id_channel=3 THEN "INTERNET BANKING"
                                                    WHEN id_channel=4 THEN "MOBILE BANKING"
                                                END AS nama_channel,                                                
                                                tanggal_bayar,
                                                nomor_rekening_pengirim,
                                                nama_rekening_pengirim,
                                                nama_bank_pengirim,
                                                total_bayar,
                                                verified,
                                                CASE 
                                                    WHEN verified IS NULL THEN "N.A"
                                                    WHEN verified=0 THEN "UNVERIFIED"
                                                    WHEN verified=1 THEN "VERIFIED"
                                                END AS nama_status,
                                                bukti_bayar,
                                                konfirmasi_pembayaran.created_at,
                                                konfirmasi_pembayaran.updated_at
                                            '));
    }

    private function findScopedKonfirmasi($query, Request $request, $id)
    {
        $formulirId = $request->input('formulir_id');
        $ta = $request->input('ta');
        $kodeJenjang = $request->input('kode_jenjang');

        return (clone $query)
            ->where(function ($q) use ($id) {
                $q->where('user_id', $id)->orWhere('transaksi_id', $id);
            })
            ->where(function ($q) use ($formulirId, $ta, $kodeJenjang) {
                $q->where(function ($q2) {
                    $q2->whereNull('formulir_id')->whereNull('ta');
                });
                if ($formulirId) {
                    $q->orWhere('formulir_id', $formulirId);
                }
                if ($ta !== null && $ta !== '') {
                    $q->orWhere(function ($q2) use ($ta, $kodeJenjang) {
                        $q2->where('ta', $ta);
                        if ($kodeJenjang !== null && $kodeJenjang !== '') {
                            $q2->where('kode_jenjang', $kodeJenjang);
                        }
                    });
                }
            })
            ->orderByRaw('
                CASE
                    WHEN formulir_id = ? THEN 0
                    WHEN ta = ? AND kode_jenjang = ? THEN 1
                    WHEN verified = 0 THEN 2
                    ELSE 3
                END
            ', [$formulirId, $ta, $kodeJenjang])
            ->orderBy('created_at', 'desc')
            ->first();
    }
}
