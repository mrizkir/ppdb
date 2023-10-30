<template>
    <SPSBLayout>
        <ModuleHeader v-if="dashboard!='siswabaru'">

        </ModuleHeader>
        <v-container fluid v-if="dashboard== 'siswabaru'">
            <v-row align="center" justify="center" class="mb-4" no-gutters>
                <v-col xs="12" sm="12" md="7">
                    <v-alert type="info">
                        Lakukan unggah persyaratan secara bertahap, klik tombol Unggah setelah melampirkan file.
                    </v-alert>
                </v-col>
                <v-col xs="12" sm="12" md="7">
                    <v-form v-model="form_valid_foto_selfi" ref="frmuploadfotoselfi" lazy-validation>
                        <v-card class="mb-3">
                            <v-card-title>
                                FOTO SELFIE
                            </v-card-title>
                            <v-card-text>
                                Foto "Selfie" Keluarga yang terdiri dari (Kedua Orangtua/wali dan Calon Peserta Didik)
                                <v-file-input 
                                    accept="application/pdf,image/jpeg,image/png" 
                                    label="(.pdf, .png, atau .jpg) MAX 2MB"
                                    :rules="rule_filefotoselfi"
                                    show-size
                                    v-model="filefotoselfi">
                                </v-file-input>
                            </v-card-text>
                            <v-card-actions>      
                                <v-btn
                                    color="green"
                                    text
                                    :href="this.$api.url+'/'+peryaratanppdb.file_fotoselfi"
                                    v-if="peryaratanppdb.file_fotoselfi">                                                                      
                                    LIHAT
                                </v-btn>                               
                                <v-spacer/>
                                <v-btn 
                                    color="grey darken-1" 
                                    text 
                                    @click.stop="$router.push('/dashboard/'+$store.getters['auth/AccessToken'])">KEMBALI</v-btn>                                                            
                                <v-btn
                                    color="orange"
                                    text
                                    @click="uploadFotoSelfi"
                                    :loading="btnLoadingFotoSelfi"                       
                                    :disabled="!form_valid_foto_selfi||btnLoadingFotoSelfi">                                   
                                    UNGGAH
                                </v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-form>
                    <v-form v-model="form_valid_ktp_ayah" ref="frmuploadktpayah" lazy-validation>
                        <v-card class="mb-3">
                            <v-card-title>
                                SCAN KTP AYAH/WALI
                            </v-card-title>
                            <v-card-text>
                                Scan sisi depan KTP ayah/wali.
                                <v-file-input 
                                    accept="application/pdf,image/jpeg,image/png" 
                                    label="(.pdf, .png, atau .jpg) MAX 2MB"
                                    :rules="rule_file_ktp_ayah"
                                    show-size
                                    v-model="filektpayah">
                                </v-file-input>
                            </v-card-text>
                            <v-card-actions>      
                                <v-btn
                                    color="green"
                                    text
                                    :href="this.$api.url+'/'+peryaratanppdb.file_ktp_ayah"
                                    v-if="peryaratanppdb.file_ktp_ayah">                                                                      
                                    LIHAT
                                </v-btn>                               
                                <v-spacer/>
                                <v-btn 
                                    color="grey darken-1" 
                                    text 
                                    @click.stop="$router.push('/dashboard/'+$store.getters['auth/AccessToken'])">KEMBALI</v-btn>                                   
                                <v-btn
                                    color="orange"
                                    text
                                    @click="uploadKTPAyah"
                                    :loading="btnLoadingKTPAyah"                       
                                    :disabled="!form_valid_ktp_ayah||btnLoadingKTPAyah">                                   
                                    UNGGAH
                                </v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-form>
                    <v-form v-model="form_valid_ktp_ibu" ref="frmuploadktpibu" lazy-validation>
                        <v-card class="mb-3">
                            <v-card-title>
                                SCAN KTP IBU/AYAH WALI
                            </v-card-title>
                            <v-card-text>
                                Scan sisi depan KTP ibu/wali.
                                <v-file-input 
                                    accept="application/pdf,image/jpeg,image/png" 
                                    label="(.pdf, .png, atau .jpg) MAX 2MB"
                                    :rules="rule_file_ktp_ibu"
                                    show-size
                                    v-model="filektpibu">
                                </v-file-input>
                            </v-card-text>
                            <v-card-actions>      
                                <v-btn
                                    color="green"
                                    text
                                    :href="this.$api.url+'/'+peryaratanppdb.file_ktp_ibu"
                                    v-if="peryaratanppdb.file_ktp_ibu">                                                                      
                                    LIHAT
                                </v-btn>                               
                                <v-spacer/>
                                <v-btn 
                                    color="grey darken-1" 
                                    text 
                                    @click.stop="$router.push('/dashboard/'+$store.getters['auth/AccessToken'])">KEMBALI</v-btn>                                                                         
                                <v-btn
                                    color="orange"
                                    text
                                    @click="uploadKTPIbu"
                                    :loading="btnLoadingKTPIbu"                       
                                    :disabled="!form_valid_ktp_ibu||btnLoadingKTPIbu">                                   
                                    UNGGAH
                                </v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-form>
                    <v-form v-model="form_valid_kk" ref="frmuploadkk" lazy-validation>
                        <v-card class="mb-3">
                            <v-card-title>
                                SCAN KARTU KELUARGA 
                            </v-card-title>
                            <v-card-text>
                                Scan Kartu Keluarga.
                                <v-file-input 
                                    accept="application/pdf,image/jpeg,image/png" 
                                    label="(.pdf, .png, atau .jpg) MAX 2MB"
                                    :rules="rule_filekk"
                                    show-size
                                    v-model="filekk">
                                </v-file-input>
                            </v-card-text>
                            <v-card-actions>      
                                <v-btn
                                    color="green"
                                    text
                                    :href="this.$api.url+'/'+peryaratanppdb.file_kk"
                                    v-if="peryaratanppdb.file_kk">                                                                      
                                    LIHAT
                                </v-btn>                               
                                <v-spacer/>
                                <v-btn 
                                    color="grey darken-1" 
                                    text 
                                    @click.stop="$router.push('/dashboard/'+$store.getters['auth/AccessToken'])">KEMBALI</v-btn>                                                                     
                                <v-btn
                                    color="orange"
                                    text
                                    @click="uploadKK"
                                    :loading="btnLoadingKK"                       
                                    :disabled="!form_valid_kk||btnLoadingKK">                                   
                                    UNGGAH
                                </v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-form>
                    <v-form v-model="form_valid_aktalahir" ref="frmuploadaktalahir" lazy-validation>
                        <v-card class="mb-3">
                            <v-card-title>
                                SCAN AKTA KELAHIRAN
                            </v-card-title>
                            <v-card-text>
                                Scan Akta Kelahiran Calon Peserta Didik.
                                <v-file-input 
                                    accept="application/pdf,image/jpeg,image/png" 
                                    label="(.pdf, .png, atau .jpg) MAX 2MB"
                                    :rules="rule_fileaktalahir"
                                    show-size
                                    v-model="fileaktalahir">
                                </v-file-input>
                            </v-card-text>
                            <v-card-actions>      
                                <v-btn
                                    color="green"
                                    text
                                    :href="this.$api.url+'/'+peryaratanppdb.file_aktalahir"
                                    v-if="peryaratanppdb.file_aktalahir">                                                                      
                                    LIHAT
                                </v-btn>                               
                                <v-spacer/>
                                <v-btn 
                                    color="grey darken-1" 
                                    text 
                                    @click.stop="$router.push('/dashboard/'+$store.getters['auth/AccessToken'])">KEMBALI</v-btn>                                                                      
                                <v-btn
                                    color="orange"
                                    text
                                    @click="uploadAktaLahir"
                                    :loading="btnLoadingAktaLahir"                       
                                    :disabled="!form_valid_aktalahir||btnLoadingAktaLahir">                                   
                                    UNGGAH
                                </v-btn>
                            </v-card-actions>
                        </v-card>
                    </v-form>
                </v-col>
            </v-row>
        </v-container>
    </SPSBLayout>
</template>
<script>
import SPSBLayout from '@/views/layouts/SPSBLayout';
import ModuleHeader from '@/components/ModuleHeader';

export default {
    name: 'PersyaratanPPDBTambah', 
    created() {
        this.dashboard = this.$store.getters['uiadmin/getDefaultDashboard'];
        if (this.dashboard == 'siswabaru')
        {
            this.pesertadidik_id=this.$store.getters['auth/AttributeUser']('id');
        }
        this.initialize()
    },
    data: () => ({ 
        dashboard: null,
        peryaratanppdb: {},
        //formdata
        form_valid_foto_selfi: true,
        form_valid_ktp_ayah: true,
        form_valid_ktp_ibu: true,
        form_valid_kk: true,
        form_valid_aktalahir: true,
        pesertadidik_id: null,

        btnLoadingFotoSelfi: false,
        btnLoadingKTPAyah: false,
        btnLoadingKK: false,
        btnLoadingAktaLahir: false,

        filefotoselfi: null,
        filektpayah: null,
        filektpibu: null,
        filekk: null,
        fileaktalahir: null,

        rule_filefotoselfi: [
            value => !!value || "Mohon pilih file foto selfi !!!",  
            value =>  !value || value.size < 2000000 || 'File foto selfi harus kurang dari 2MB.'                
        ],
        rule_file_ktp_ayah: [
            value => !!value || "Mohon pilih file ktp !!!",  
            value =>  !value || value.size < 2000000 || 'File ktp harus kurang dari 2MB.'                
        ],
        rule_file_ktp_ibu: [
            value => !!value || "Mohon pilih file ktp !!!",  
            value =>  !value || value.size < 2000000 || 'File ktp harus kurang dari 2MB.'                
        ],
        rule_filekk: [
            value => !!value || "Mohon pilih file Kartu Keluarga !!!",  
            value =>  !value || value.size < 2000000 || 'File kartu keluarga harus kurang dari 2MB.'                
        ],
        rule_fileaktalahir: [
            value => !!value || "Mohon pilih file Akta Lahir !!!",  
            value =>  !value || value.size < 2000000 || 'File akta lahir harus kurang dari 2MB.'                
        ],
    }),
    methods: {
        initialize: async function()
		{	
            await this.$ajax.get('/spsb/formulirpendaftaran/persyaratanppdb/'+this.$store.getters['auth/AttributeUser']('id'),  
                {
                    headers: {
                        Authorization: this.$store.getters["auth/Token"]
                    }
                },
                
            ).then(({ data }) => {   
                this.peryaratanppdb = data.formulir;
                this.$refs.frmuploadfotoselfi.resetValidation();
            });

        },
        async uploadFotoSelfi ()
        {
            if (this.$refs.frmuploadfotoselfi.validate())
            {
                if (typeof this.filefotoselfi !== 'undefined' && this.filefotoselfi !== null )
                {
                    this.btnLoadingFotoSelfi=true;
                    var formdata = new FormData();
                    formdata.append('filefotoselfi',this.filefotoselfi);
                    await this.$ajax.post('/spsb/formulirpendaftaran/uploadfileselfi/'+this.pesertadidik_id,formdata, 
                        {
                            headers: {
                                Authorization: this.$store.getters["auth/Token"],
                                'Content-Type': 'multipart/form-data'                      
                            }
                        }
                    ).then(()=>{                                                            
                        this.btnLoadingFotoSelfi=false;
                        this.$router.go();
                    }).catch(()=>{
                        this.btnLoadingFotoSelfi=false;
                    });
                }
            }
        }, 
        async uploadKTPAyah ()
        {
            if (this.$refs.frmuploadktpayah.validate())
            {
                if (typeof this.filektpayah !== 'undefined' && this.filektpayah !== null )
                {
                    this.btnLoadingKTPAyah=true;
                    var formdata = new FormData();
                    formdata.append('filektpayah',this.filektpayah);
                    await this.$ajax.post('/spsb/formulirpendaftaran/uploadfilektpayah/'+this.pesertadidik_id,formdata, 
                        {
                            headers: {
                                Authorization: this.$store.getters["auth/Token"],
                                'Content-Type': 'multipart/form-data'                      
                            }
                        }
                    ).then(()=>{                                                            
                        this.btnLoadingKTPAyah=false;
                        this.$router.go();
                    }).catch(()=>{
                        this.btnLoadingKTPAyah=false;
                    });
                }
            }
        }, 
        async uploadKTPIbu ()
        {
            if (this.$refs.frmuploadktpibu.validate())
            {
                if (typeof this.filektpibu !== 'undefined' && this.filektpibu !== null )
                {
                    this.btnLoadingKTPIbu=true;
                    var formdata = new FormData();
                    formdata.append('filektpibu',this.filektpibu);
                    await this.$ajax.post('/spsb/formulirpendaftaran/uploadfilektpibu/'+this.pesertadidik_id,formdata, 
                        {
                            headers: {
                                Authorization: this.$store.getters["auth/Token"],
                                'Content-Type': 'multipart/form-data'                      
                            }
                        }
                    ).then(()=>{                                                            
                        this.btnLoadingKTPIbu=false;
                        this.$router.go();
                    }).catch(()=>{
                        this.btnLoadingKTPIbu=false;
                    });
                }
            }
        }, 
        async uploadKK ()
        {
            if (this.$refs.frmuploadkk.validate())
            {
                if (typeof this.filekk !== 'undefined' && this.filekk !== null )
                {
                    this.btnLoadingKK=true;
                    var formdata = new FormData();
                    formdata.append('filekk',this.filekk);
                    await this.$ajax.post('/spsb/formulirpendaftaran/uploadfilekk/'+this.pesertadidik_id,formdata, 
                        {
                            headers: {
                                Authorization: this.$store.getters["auth/Token"],
                                'Content-Type': 'multipart/form-data'                      
                            }
                        }
                    ).then(()=>{                                                            
                        this.btnLoadingKK=false;
                        this.$router.go();
                    }).catch(()=>{
                        this.btnLoadingKK=false;
                    });
                }
            }
        }, 
        async uploadAktaLahir ()
        {
            if (this.$refs.frmuploadaktalahir.validate())
            {
                if (typeof this.fileaktalahir !== 'undefined' && this.fileaktalahir !== null )
                {
                    this.btnLoadingAktaLahir=true;
                    var formdata = new FormData();
                    formdata.append('fileaktalahir',this.fileaktalahir);
                    await this.$ajax.post('/spsb/formulirpendaftaran/uploadfileaktalahir/'+this.pesertadidik_id,formdata, 
                        {
                            headers: {
                                Authorization: this.$store.getters["auth/Token"],
                                'Content-Type': 'multipart/form-data'                      
                            }
                        }
                    ).then(()=>{                                                            
                        this.btnLoadingAktaLahir=false;
                        this.$router.go();
                    }).catch(()=>{
                        this.btnLoadingAktaLahir=false;
                    });
                }
            }
        }, 
    },
    components: {
        SPSBLayout,
        ModuleHeader,
    },
}
</script>