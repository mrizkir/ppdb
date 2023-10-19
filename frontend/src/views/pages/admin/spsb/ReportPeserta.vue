<template>
    <SPSBLayout>
        <ModuleHeader>
            <template v-slot:icon>
                mdi-account-plus
            </template>
            <template v-slot:name>
                LAPORAN CALON PESERTA DIDIK
            </template>
            <template v-slot:subtitle>
                TAHUN PENDAFTARAN {{tahun_pendaftaran}} - {{nama_jenjang}}
            </template>
            <template v-slot:breadcrumbs>
                <v-breadcrumbs :items="breadcrumbs" class="pa-0">
                    <template v-slot:divider>
                        <v-icon>mdi-chevron-right</v-icon>
                    </template>
                </v-breadcrumbs>
            </template>
            <template v-slot:desc>
                <v-alert                                        
                    color="cyan"
                    border="left"           
                    colored-border
                    type="info"
                    >
                        Halaman ini berisi laporan per calon peserta didik 
                    </v-alert>
            </template>
        </ModuleHeader>  
        <v-container fluid>    
            <v-row class="mb-4" no-gutters>
                <v-col cols="12">
                    <v-card>
                        <v-card-text>
                            <v-text-field
                                v-model="search"
                                append-icon="mdi-database-search"
                                label="Search"
                                single-line
                                hide-details
                            ></v-text-field>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
            <v-row class="mb-4" no-gutters>
                <v-col cols="12">
                    <v-data-table
                        :headers="headers"
                        :items="datatable"
                        :search="search"
                        item-key="id"
                        sort-by="name"
                        show-expand
                        :expanded.sync="expanded"
                        :single-expand="true"
                        @click:row="dataTableRowClicked"
                        class="elevation-1"
                        :loading="datatableLoading"
                        loading-text="Loading... Please wait">
                        <template v-slot:top>
                            <v-toolbar flat color="white">                                
                                <v-spacer></v-spacer>
                            </v-toolbar>
                        </template>                        
                        <template v-slot:item.foto="{ item }">    
                            <v-badge
                                bordered
                                :color="badgeColor(item)"
                                :icon="badgeIcon(item)"
                                overlap>                
                                <v-avatar size="30">                                        
                                    <v-img :src="$api.url+'/'+item.foto" />                                                                     
                                </v-avatar>                                                                                                  
                            </v-badge>
                        </template>
                        <template v-slot:item.file_fotoselfi="{ item }">                            
                            {{item.file_fotoselfi==null ?'BELUM DIUNGGAH': 'TELAH DIUNGGAH'}}<br>                            
                        </template>
                        <template v-slot:item.file_ktp_ayah="{ item }">                            
                            {{item.file_ktp_ayah==null || item.file_ktp_ibu==null ?'BELUM DIUNGGAH': 'TELAH DIUNGGAH'}} <br >                            
                        </template>
                        <template v-slot:item.file_kk="{ item }">                            
                            {{item.file_kk==null ?'BELUM DIUNGGAH': 'TELAH DIUNGGAH'}}<br>                             
                        </template>
                        <template v-slot:item.file_aktalahir="{ item }">                            
                            {{item.file_aktalahir==null ?'BELUM DIUNGGAH': 'TELAH DIUNGGAH'}}<br>                            
                        </template>
                        <template v-slot:expanded-item="{ headers, item }">
                            <td :colspan="headers.length" class="text-center">
                                <v-col cols="12">
                                    <strong>ID:</strong>{{ item.id }}                     
                                    <strong>USERNAME:</strong>{{ item.username }}                     
                                    <strong>created_at:</strong>{{ $date(item.created_at).format('DD/MM/YYYY HH:mm') }}
                                    <strong>updated_at:</strong>{{ $date(item.updated_at).format('DD/MM/YYYY HH:mm') }}
                                </v-col>                                
                            </td>
                        </template>
                        <template v-slot:item.biodata="{ item }">
                            <v-icon
                                small
                                class="mr-2"
                                :loading="btnLoading"
                                :disabled="btnLoading"
                                @click.stop="printBiodata(item)"
                            >
                                mdi-printer
                            </v-icon>                            
                        </template>
                        <template v-slot:no-data>
                            Data belum tersedia
                        </template>
                    </v-data-table>
                </v-col>
            </v-row>
            <v-dialog v-model="dialogprintpdf" max-width="500px" persistent>                
                <v-card>
                    <v-card-title>
                        <span class="headline">Print to PDF</span>
                    </v-card-title>
                    <v-card-text>
                        <v-btn
                            color="green"
                            text
                            :href="this.$api.url+'/'+file_pdf">                            
                            Download
                        </v-btn>                           
                    </v-card-text>
                    <v-card-actions>
                        <v-spacer></v-spacer>
                        <v-btn color="blue darken-1" text @click.stop="closedialogprintpdf">BATAL</v-btn>                            
                    </v-card-actions>
                </v-card>            
            </v-dialog>
        </v-container>
        <template v-slot:filtersidebar>
            <Filter7 v-on:changeTahunPendaftaran="changeTahunPendaftaran" v-on:changeJenjang="changeJenjang" ref="filter7" />	
        </template>
    </SPSBLayout>
</template>
<script>
import SPSBLayout from '@/views/layouts/SPSBLayout';
import ModuleHeader from '@/components/ModuleHeader';
import Filter7 from '@/components/sidebar/FilterMode7';
export default {
    name: 'PendaftaranBaru',  
    created() {
        this.dashboard = this.$store.getters['uiadmin/getDefaultDashboard'];      
        this.breadcrumbs = [
            {
                text: 'HOME',
                disabled: false,
                href: '/dashboard/'+this.$store.getters['auth/AccessToken']
            },
            {
                text: 'SPSB',
                disabled: false,
                href: '/spsb'
            },
            {
                text: 'LAPORAN CALON PESERTA DIDIK',
                disabled: true,
                href: '#'
            }
        ];   
        this.breadcrumbs[1].disabled=(this.dashboard== 'siswabaru'||this.dashboard== 'mahasiswa');

        let kode_jenjang=this.$store.getters['uiadmin/getKodeJenjang'];
        this.kode_jenjang=kode_jenjang;
        this.nama_jenjang=this.$store.getters['uiadmin/getNamaJenjang'](kode_jenjang);        
        this.tahun_pendaftaran=this.$store.getters['uiadmin/getTahunPendaftaran'];        
        this.initialize();
    },
    data: () => ({ 
        firstloading: true,
        kode_jenjang:null,
        tahun_pendaftaran:null,
        nama_jenjang:null,
        
        breadcrumbs: [],
        dashboard:null,
        datatableLoading: false,
        btnLoading: false,
                  
        //tables
        headers: [                        
            { text: '', value: 'foto', width:70 }, 
            { text: 'NAMA PESERTA DIDIK', value: 'name',width:350,sortable: true },
            { text: 'NOMOR HP', value: 'nomor_hp',sortable: true },
            { text: 'FOTO SELFIE', value: 'file_fotoselfi',sortable: false}, 
            { text: 'KTP', value: 'file_ktp_ayah',sortable: false},  
            { text: 'KK', value: 'file_kk',sortable: true},  
            { text: 'AKTA LAHIR', value: 'file_aktalahir',sortable: true}, 
            { text: 'BIODATA', value: 'biodata', sortable: false,width:100 },
        ],
        expanded: [],
        search: "",
        datatable: [],

        dialogprintpdf: false,
        file_pdf:null
        
    }),
    methods: {
        changeTahunPendaftaran (tahun)
        {
            this.tahun_pendaftaran=tahun;
        },
        changeJenjang (id)
        {
            this.kode_jenjang=id;
        },
        initialize: async function() 
        {
            this.datatableLoading=true;            
            await this.$ajax.post('/spsb/psbpersyaratan',
            {
                TA: this.tahun_pendaftaran,
                kode_jenjang: this.kode_jenjang,
            },
            {
                headers: {
                    Authorization: this.$store.getters["auth/Token"]
                }
            }).then(({ data })=>{               
                this.datatable = data.psb;                
                this.datatableLoading=false;
            });          
            this.firstloading=false;
            this.$refs.filter7.setFirstTimeLoading(this.firstloading); 
        },
        badgeColor(item)
        {
            return item.active == 1 ? 'success': 'error'
        },
        badgeIcon(item)
        {
            return item.active == 1 ? 'mdi-check-bold': 'mdi-close-thick'
        },   
        dataTableRowClicked(item)
        {
            if (item === this.expanded[0])
            {
                this.expanded = [];        
            }
            else
            {
                this.expanded = [item];
            }
        },    
        async printBiodata(item)
        {
            this.datatableLoading=true;            
            await this.$ajax.post('/report/calonpesertadidik/printpdf',
            {
                user_id:item.id,
            },
            {
                headers: {
                    Authorization: this.$store.getters["auth/Token"]
                }
            }).then(({ data })=>{               
                this.file_pdf = data.pdf_file;
                this.dialogprintpdf=true;
                this.datatableLoading=false;
            }).catch(()=>{
                this.datatableLoading=false;
            });          
        },
        closedialogprintpdf () {                  
            setTimeout(() => {
                this.file_pdf=null;
                this.dialogprintpdf = false;      
                }, 300
            );
        },
    },
    watch: {
        tahun_pendaftaran()
        {
            if (!this.firstloading)
            {
                this.initialize();
            }            
        },
        kode_jenjang(val)
        {
            if (!this.firstloading)
            {
                this.nama_jenjang=this.$store.getters['uiadmin/getNamaJenjang'](val);
                this.initialize();
            }            
        },
    },
    computed: {        
        formTitle () {
            return this.editedIndex === -1 ? 'TAMBAH DATA' : 'UBAH DATA'
        },
    },
    
    components: {
        SPSBLayout,
        ModuleHeader, 
        Filter7    
    },
}
</script>