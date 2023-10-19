<template>
    <FrontLayout>        
        <v-container class="fill-height" fluid v-if="bukaPPDB">
            <v-row align="center" justify="center" no-gutters>
                <v-col cols="12">
                    <h1 class="text-center display-1 font-weight-black primary--text">
                        KONFIRMASI PEMBAYARAN
                    </h1>                        
                </v-col>
                <v-col xs="12" md="6" sm="12">
                    <v-form ref="frmkonfirmasi" @keyup.native.enter="checkUsername" lazy-validation v-if="data_pd==null">
                        <v-card outlined>
                            <v-card-title>
                                Masukan Username :
                            </v-card-title>
                            <v-card-text>
                                <v-text-field 
                                    v-model="formdata.username"
                                    label="USERNAME" 
                                    :rules="rule_username"
                                    outlined 
                                    dense />                           
                            </v-card-text>
                            <v-card-actions class="justify-center">
                                <v-btn 
                                    color="primary" 
                                    @click="checkUsername" 
                                    :loading="btnLoading"
                                    :disabled="btnLoading"
                                    block>
                                        Konfirmasi
                                </v-btn>	
                            </v-card-actions>
                        </v-card>                        
                    </v-form>
                    <v-form ref="frmkonfirmasi" @keyup.native.enter="save" lazy-validation v-else>
                        <v-card outlined class="mb-2">                                                        
                            <v-card-text>
                                <v-card flat>
                                    <v-card-title>NAMA PESERTA DIDIK:</v-card-title>  
                                    <v-card-subtitle>
                                        {{data_pd.name}}
                                    </v-card-subtitle>
                                </v-card>
                                <v-card flat>
                                    <v-card-title>NOMOR KONTAK WA:</v-card-title>  
                                    <v-card-subtitle>
                                        {{data_pd.nomor_hp}}
                                    </v-card-subtitle>
                                </v-card>
                                <v-card flat>
                                    <v-card-title>EMAIL:</v-card-title>  
                                    <v-card-subtitle>
                                        {{data_pd.email}}
                                    </v-card-subtitle>
                                </v-card>
                                <v-card flat>
                                    <v-card-title>BIAYA + KODE TRANSFER:</v-card-title>  
                                    <v-card-subtitle>
                                        {{data_pd.code|formatUang}}
                                    </v-card-subtitle>
                                </v-card>
                            </v-card-text>                            
                        </v-card>  
                        <v-card>                                                            
                            <v-card-text>  
                                <v-select
                                    label="PEMBAYARAN MELALUI :"
                                    v-model="formdata.id_channel"
                                    :items="daftar_channel"
                                    item-text="nama_channel"
                                    item-value="id_channel"
                                    :rules="rule_channel_pembayaran"
                                    outlined
                                />  
                                <v-text-field
                                    v-model="formdata.total_bayar"
                                    label="SEBESAR :"
                                    :rules="rule_total_bayar"
                                    outlined
                                />
                                <v-text-field 
                                    v-model="formdata.nomor_rekening_pengirim"
                                    label="NOMOR REKENING PENGIRIM:" 
                                    :rules="rule_nomor_rekening"
                                    outlined />  
                                <v-text-field 
                                    v-model="formdata.nama_rekening_pengirim"
                                    label="NAMA PENGIRIM:" 
                                    :rules="rule_nama_pengirim"
                                    outlined />  
                                <v-text-field 
                                    v-model="formdata.nama_bank_pengirim"
                                    label="BANK PENGIRIM:" 
                                    :rules="rule_nama_bank"
                                    outlined /> 
                                <v-menu
                                    ref="menuTanggalBayar"
                                    v-model="menuTanggalBayar"
                                    :close-on-content-click="false"
                                    :return-value.sync="formdata.tanggal_bayar"
                                    transition="scale-transition"
                                    offset-y
                                    max-width="290px"
                                    min-width="290px"
                                >
                                    <template v-slot:activator="{ on }">
                                        <v-text-field
                                            v-model="formdata.tanggal_bayar"
                                            label="TANGGAL BAYAR/TRANSFER"                                   
                                            readonly
                                            outlined
                                            v-on="on"
                                            :rules="rule_tanggal_bayar"
                                        ></v-text-field>
                                    </template>
                                    <v-date-picker
                                        v-model="formdata.tanggal_bayar"                               
                                        no-title                                
                                        scrollable
                                        >
                                        <v-spacer></v-spacer>
                                        <v-btn text color="primary" @click="menuTanggalBayar = false">Cancel</v-btn>
                                        <v-btn text color="primary" @click="$refs.menuTanggalBayar.save(formdata.tanggal_bayar)">OK</v-btn>
                                    </v-date-picker>
                                </v-menu>
                                <v-textarea 
                                    v-model="formdata.desc"
                                    label="CATATAN:"                                                           
                                    outlined />
                                <v-file-input 
                                    accept="image/jpeg,image/png" 
                                    label="BUKTI BAYAR (MAKS. 2MB)"
                                    :rules="rule_bukti_bayar"
                                    show-size
                                    v-model="formdata.bukti_bayar"
                                    @change="previewImage">
                                </v-file-input> 
                                <v-img class="white--text align-end" :src="buktiBayar"></v-img>                                                                               
                            </v-card-text>
                            <v-card-actions>
                                <v-spacer></v-spacer>
                                <v-btn color="blue darken-1" text @click.stop="closedialogfrm">BATAL</v-btn>
                                <v-btn 
                                    color="blue darken-1" 
                                    text 
                                    @click.stop="save" 
                                    :loading="btnLoading"
                                    :disabled="!form_valid || btnLoading">
                                        SIMPAN
                                </v-btn>
                            </v-card-actions>
                        </v-card>                      
                    </v-form>
                </v-col>
            </v-row>
        </v-container>
    </FrontLayout>
</template>
<script>
import { mapGetters } from 'vuex';
import FrontLayout from '@/views/layouts/FrontLayout';
export default {
    name: 'KonfirmasiPembayaran',
    data: () => ({     
        btnLoading: false,
        //form
        form_valid: true,
        data_pd: null,

        menuTanggalBayar: false,
        image_prev: null,
        daftar_channel: [
            {
                id_channel:1,
                nama_channel: 'TELLER BANK'
            },
            {
                id_channel:2,
                nama_channel: 'TRANSFER BANK'
            },
            {
                id_channel:3,
                nama_channel: 'INTERNET BANKING'
            },
            {
                id_channel:4,
                nama_channel: 'MOBILE BANKING'
            },
        ],
        formdata: {
            id: "",
            username: "",
            id_channel:2, 
            total_bayar:0,
            nomor_rekening_pengirim: "",
            nama_rekening_pengirim: "",
            nama_bank_pengirim: "",
            desc: "",
            tanggal_bayar: "",
            bukti_bayar: [],
        },
        rule_username: [
            value => !!value||"Kolom Username mohon untuk diisi !!!"
        ], 
        //form rules  
        rule_channel_pembayaran: [
            value => !!value||"Mohon dipilih Channel Pembayaran mohon untuk dipilih !!!"
        ], 
        rule_nama_pengirim: [
            value => !!value||"Mohon diisi nama pengirim !!!"
        ],
        rule_nomor_rekening: [
            value => !!value||"Mohon diisi nomor rekening pengirim !!!",
            value => /^[0-9]+$/.test(value) || 'Nomor Rekening hanya boleh angka',
        ],
        rule_nama_bank: [
            value => !!value||"Mohon diisi nama bank !!!"
        ],
        rule_tanggal_bayar: [
            value => !!value||"Tanggal Bayar mohon untuk diisi !!!"
        ], 
        rule_bukti_bayar: [
            value => !!value||"Mohon pilih foto !!!",  
            value =>  !value || value.size < 2000000 || 'File Bukti Bayar harus kurang dari 2MB.'                
        ],
        rule_total_bayar: [
            value => !!value||"Dana yang  ditransfer mohon untuk untuk di isi !!!",
            value => /^[0-9]+$/.test(value) || 'Dana yang  ditransfer hanya boleh angka',  
        ], 
    }),
    methods: {
        async checkUsername ()
        {
            if (this.$refs.frmkonfirmasi.validate())
            {
                this.btnLoading = true;
                await this.$ajax.post('/spsb/psb/konfirmasi', {
                    username: this.formdata.username, 
                }).then(({ data })=>{  
                    this.data_pd = data.user;
                    this.formdata.id = data.user.id;
                    this.formdata.username = data.user.username;
                    this.btnLoading = false;
                }).catch(() => {        
                    this.btnLoading = false;
                });                                
            }
        },
        previewImage (e)
        {
            if (typeof e === 'undefined')
            {
                this.image_prev=null;                
            }
            else
            {
                let reader = new FileReader();
                reader.readAsDataURL(e);
                reader.onload = img => {                    
                    this.image_prev=img.target.result;
                } 
            }          
        },
          save () {
            if (this.$refs.frmkonfirmasi.validate())
            {
                this.btnLoading = true;      
                var data = new FormData();                                    
                data.append('user_id',this.formdata.id);
                data.append('transaksi_id',this.data_pd.code);
                data.append('id_channel',this.formdata.id_channel);
                data.append('total_bayar',this.formdata.total_bayar);
                data.append('nomor_rekening_pengirim',this.formdata.nomor_rekening_pengirim);
                data.append('nama_rekening_pengirim',this.formdata.nama_rekening_pengirim);
                data.append('nama_bank_pengirim',this.formdata.nama_bank_pengirim);
                data.append('desc',this.formdata.desc);
                data.append('tanggal_bayar',this.formdata.tanggal_bayar);
                data.append('bukti_bayar',this.formdata.bukti_bayar);

                this.$ajax.post('/spsb/psb/konfirmasipembayaran',data, 
                    {
                        headers: {                            
                            'Content-Type': 'multipart/form-data'
                        }
                    }
                ).then(()=>{               
                    this.btnLoading = false;                              
                    this.$router.go();
                }).catch(()=>{
                    this.btnLoading = false;
                });
            
            }
        },
    },
    computed: {
        buktiBayar: {
            get ()
            {   
                if (this.image_prev==null)
                {
                    return require('@/assets/no-image.png');
                }
                else
                {
                    return this.image_prev;
                }
            },
            set (val)
            {
                this.image_prev=val;
            }
            
        },
        ...mapGetters('uifront',{            
            bukaPPDB: 'getBukaPPDB',
        }),   
    },
    components: {
        FrontLayout,
    }, 
}
</script>