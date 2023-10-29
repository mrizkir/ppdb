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
    
    public function show(Request $request,$id)
    {
        $konfirmasi=KonfirmasiPembayaranModel::select(\DB::raw('
                                                transaksi_id,
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
                                                CASE 
                                                    WHEN verified IS NULL THEN "N.A"
                                                    WHEN verified=0 THEN "UNVERIFIED"
                                                    WHEN verified=1 THEN "VERIFIED"
                                                END AS nama_status,
                                                bukti_bayar,
                                                konfirmasi_pembayaran.created_at,
                                                konfirmasi_pembayaran.updated_at
                                            '))                                            
                                            ->find($id);

        if (is_null($konfirmasi))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'fetchdata',                
                                    'message'=>["Fetch data transaksi dengan ID ($id) gagal diperoleh di KONFIRMASI PEMBAYARAN"]
                                ],422); 
        }
        else
        {
            return Response()->json([
                                        'status'=>1,
                                        'pid'=>'fetchdata',  
                                        'konfirmasi'=>$konfirmasi,                                                                                                                                   
                                        'message'=>'Fetch data detail konfirmasi berhasil.'
                                    ],200);     
        }
    }    
    /**
     * digunakan untuk merubah status transaksi menjadi paid
     */
    public function update(Request $request,$id)
    {
        $this->hasPermissionTo('KEUANGAN-KONFIRMASI-PEMBAYARAN_UPDATE');

        $konfirmasi=KonfirmasiPembayaranModel::find($id);
        if (is_null($konfirmasi))
        {
            return Response()->json([
                                    'status'=>0,
                                    'pid'=>'update',                
                                    'message'=>["Update data transaksi dengan ID ($id) gagal diperoleh di KONFIRMASI PEMBAYARAN"]
                                ],422); 
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
                                    ],200);   
        }
        
    }
}