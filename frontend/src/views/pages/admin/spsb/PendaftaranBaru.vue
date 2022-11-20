<template>
  <SPSBLayout>
    <ModuleHeader>
      <template v-slot:icon>
        mdi-account-plus
      </template>
      <template v-slot:name>
        PENDAFTAR 
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
            Halaman ini berisi pendaftar baru.
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
                <v-btn 
                  :loading="btnLoading"
                  :disabled="btnLoading"
                  color="warning" 
                  class="mb-2 mr-2" 
                  @click.stop="syncPermission" 
                  v-if="$store.getters['auth/can']('USER_STOREPERMISSIONS')">
                  SYNC PERMISSION
                </v-btn>
                <v-btn color="primary" 
                  class="mb-2" 
                  @click.stop="addItem">
                    TAMBAH
                </v-btn>
                <v-dialog v-model="dialogfrm" max-width="500px" persistent>                                    
                  <v-form ref="frmdata" v-model="form_valid" lazy-validation>
                    <v-card>
                      <v-card-title>
                        <span class="headline">{{ formTitle }}</span>
                      </v-card-title>
                      <v-card-subtitle>
                        <span class="info--text">
                          Secara default akan tersimpan di jenjang <strong>{{nama_jenjang}} - {{tahun_pendaftaran}}.</strong>
                          Anda bisa merubahnya dengan memilih JENJANG STUDI atau Tahun Pelajaran dibawah ini.
                        </span>
                      </v-card-subtitle>
                      <v-card-text>
                        <v-text-field 
                          v-model="formdata.name"
                          label="NAMA LENGKAP" 
                          :rules="rule_name"
                          outlined/>     
                        
                        <v-text-field 
                          v-model="formdata.nomor_hp"
                          label="NOMOR HP (ex: +628123456789)" 
                          :rules="rule_nomorhp"
                          outlined
                        />                               
                        <v-text-field 
                          v-model="formdata.email"
                          label="EMAIL" 
                          :rules="rule_email"
                          outlined
                        />                                                       
                        <v-select
                          label="JENJANG STUDI"
                          v-model="formdata.kode_jenjang"
                          :items="daftar_jenjang"
                          item-text="nama_jenjang"
                          item-value="kode_jenjang"
                          :rules="rule_jenjang"
                          outlined
                        />
                        <v-select
                          v-model="formdata.ta"
                          :items="daftar_ta"                                           
                          label="TAHUN PENDAFTARAN"
                          outlined
                        />   
                        <v-text-field 
                          v-model="formdata.username"
                          label="USERNAME" 
                          :rules="rule_username"
                          outlined />   
                        <v-text-field 
                          v-model="formdata.password"
                          label="PASSWORD" 
                          type="password"                                                                    
                          outlined 
                          v-if="editedIndex>-1" /> 
                        <v-text-field 
                          v-model="formdata.password"
                          label="PASSWORD" 
                          type="password"
                          :rules="rule_password"       
                          outlined 
                          v-else /> 
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
                </v-dialog>
                <v-dialog v-model="dialogdetailitem" max-width="750px" persistent>
                  <v-card>
                    <v-card-title>
                      <span class="headline">DETAIL DATA</span>
                    </v-card-title>
                    <v-card-text>
                      <v-row no-gutters>
                        <v-col xs="12" sm="6" md="6">
                          <v-card flat>
                            <v-card-title>ID :</v-card-title>
                            <v-card-subtitle>
                              {{formdata.id}}
                            </v-card-subtitle>
                          </v-card>
                        </v-col>
                        <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                        <v-col xs="12" sm="6" md="6">
                          <v-card flat>
                            <v-card-title>USERNAME :</v-card-title>
                            <v-card-subtitle>
                              {{formdata.username}}
                            </v-card-subtitle>
                          </v-card>
                        </v-col>
                        <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                      </v-row>
                      <v-row no-gutters>
                        <v-col xs="12" sm="6" md="6">
                          <v-card flat>
                            <v-card-title>NAMA PESERTA DIDIK :</v-card-title>
                            <v-card-subtitle>
                              {{formdata.name}}
                            </v-card-subtitle>
                          </v-card>
                        </v-col>
                        <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                        <v-col xs="12" sm="6" md="6">
                          <v-card flat>
                            <v-card-title>NOMOR HP :</v-card-title>
                            <v-card-subtitle>
                              {{formdata.nomor_hp}}
                            </v-card-subtitle>
                          </v-card>
                        </v-col>
                        <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                      </v-row>
                      <v-row no-gutters>
                        <v-col xs="12" sm="6" md="6">
                          <v-card flat>
                            <v-card-title>EMAIL :</v-card-title>
                            <v-card-subtitle>
                              {{formdata.email}}
                            </v-card-subtitle>
                          </v-card>
                        </v-col>
                        <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                        <v-col xs="12" sm="6" md="6">
                          <v-card flat>
                            <v-card-title>CREATED/UPDATED :</v-card-title>
                            <v-card-subtitle>
                              {{$date(formdata.created_at).format('DD/MM/YYYY HH:mm')}} /  
                              {{$date(formdata.updated_at).format('DD/MM/YYYY HH:mm')}}
                            </v-card-subtitle>
                          </v-card>
                        </v-col>
                        <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                      </v-row>
                      <v-row no-gutters v-if="data_konfirmasi.bukti_bayar">
                        <v-col cols="12">
                          <v-card>
                            <v-card-title>BUKTI BAYAR TRANSFER</v-card-title>     
                            <v-card-text>                                                   
                              <v-row>
                                <v-col xs="12" sm="6" md="6">
                                  <v-card flat>
                                    <v-card-title>ID :</v-card-title>
                                    <v-card-subtitle>
                                      {{data_konfirmasi.transaksi_id}}
                                    </v-card-subtitle>
                                  </v-card>
                                </v-col>
                                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                                <v-col xs="12" sm="6" md="6">
                                  <v-card flat>
                                    <v-card-title>KODE BILLING :</v-card-title>
                                    <v-card-subtitle>
                                      {{data_konfirmasi.no_transaksi}}
                                    </v-card-subtitle>
                                  </v-card>
                                </v-col>
                                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                              </v-row>
                              <v-row>
                                <v-col xs="12" sm="6" md="6">
                                  <v-card flat>
                                    <v-card-title>CHANNEL PEMBAYARAN :</v-card-title>
                                    <v-card-subtitle>
                                      {{data_konfirmasi.nama_channel}}
                                    </v-card-subtitle>
                                  </v-card>
                                </v-col>
                                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                                <v-col xs="12" sm="6" md="6">
                                  <v-card flat>
                                    <v-card-title>TANGGAL KONFIRMASI :</v-card-title>
                                    <v-card-subtitle>
                                      {{$date(data_konfirmasi.tanggal_bayar).format('DD/MM/YYYY')}}
                                    </v-card-subtitle>
                                  </v-card>
                                </v-col>                                                
                                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                              </v-row>
                              <v-row>
                                <v-col xs="12" sm="6" md="6">
                                  <v-card flat>
                                    <v-card-title>NOMOR REKENING PENGIRIM :</v-card-title>
                                    <v-card-subtitle>
                                      {{data_konfirmasi.nomor_rekening_pengirim}}
                                    </v-card-subtitle>
                                  </v-card>
                                </v-col>                                                
                                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                                <v-col xs="12" sm="6" md="6">
                                  <v-card flat>
                                    <v-card-title>NAMA REKENING PENGIRIM :</v-card-title>
                                    <v-card-subtitle>
                                      {{data_konfirmasi.nama_rekening_pengirim}}
                                    </v-card-subtitle>
                                  </v-card>
                                </v-col>
                                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                              </v-row>
                              <v-row>
                                <v-col xs="12" sm="6" md="6">
                                  <v-card flat>
                                    <v-card-title>NAMA BANK PENGIRIM :</v-card-title>
                                    <v-card-subtitle>
                                      {{data_konfirmasi.nama_bank_pengirim}}
                                    </v-card-subtitle>
                                  </v-card>
                                </v-col>
                                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                                <v-col xs="12" sm="6" md="6">
                                  <v-card flat>
                                    <v-card-title>TOTAL BAYAR :</v-card-title>
                                    <v-card-subtitle>
                                      {{data_konfirmasi.total_bayar|formatUang}}
                                    </v-card-subtitle>
                                  </v-card>
                                </v-col>
                                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                              </v-row>
                              <v-row>
                                <v-col xs="12" sm="6" md="6">
                                  <v-card flat>
                                    <v-card-title>STATUS :</v-card-title>
                                    <v-card-subtitle>
                                      {{data_konfirmasi.nama_status}}
                                    </v-card-subtitle>
                                  </v-card>
                                </v-col>
                                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                                <v-col xs="12" sm="6" md="6">
                                  <v-card flat>
                                    <v-card-title>CREATED/UPDATED :</v-card-title>
                                    <v-card-subtitle>
                                      {{ $date(data_konfirmasi.created_at).format('DD/MM/YYYY HH:mm') }} - 
                                      {{ $date(data_konfirmasi.updated_at).format('DD/MM/YYYY HH:mm') }}
                                    </v-card-subtitle>
                                  </v-card>
                                </v-col>
                                <v-responsive width="100%" v-if="$vuetify.breakpoint.xsOnly"/>
                              </v-row>                                                            
                              <v-img class="white--text align-end" :src="$api.url+'/'+data_konfirmasi.bukti_bayar" />  
                            </v-card-text>
                          </v-card>
                        </v-col>                                                
                      </v-row>
                    </v-card-text>
                    <v-card-actions>
                      <v-spacer></v-spacer>
                      <v-btn 
                        small 
                        class="primary" 
                        @click.stop="aktifkan(formdata.id)"                                
                        :disabled="btnLoading"
                        :loading="btnLoading"
                        v-if="data_konfirmasi.verified==0">
                          <v-icon>mdi-email-check</v-icon>
                          VERIFIFIKASI BUKTI BAYAR
                      </v-btn>
                      <v-btn color="blue darken-1" text @click.stop="closedialogdetailitem">KELUAR</v-btn>
                    </v-card-actions>
                  </v-card>                                    
                </v-dialog>
              </v-toolbar>
            </template>
            <template v-slot:item.actions="{ item }">
              <v-icon
                small
                class="mr-2"
                :loading="btnLoading"
                :disabled="btnLoading"
                @click.stop="viewItem(item)"
              >
                mdi-eye
              </v-icon>
              <v-icon
                small
                class="mr-2"
                :loading="btnLoading"
                :disabled="btnLoading"
                @click.stop="editItem(item)"
              >
                mdi-pencil
              </v-icon>
              <v-icon
                small
                :loading="btnLoading"
                :disabled="btnLoading"
                @click.stop="deleteItem(item)"
              >
                mdi-delete
              </v-icon>
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
            <template v-slot:item.created_at="{ item }">                            
              {{$date(item.created_at).format('DD/MM/YYYY HH:mm')}}
            </template>
            <template v-slot:expanded-item="{ headers, item }">
              <td :colspan="headers.length" class="text-center">
                <v-col cols="12">
                  <strong>ID:</strong>{{ item.id }}                                    
                  <strong>created_at:</strong>{{ $date(item.created_at).format('DD/MM/YYYY HH:mm') }}
                  <strong>updated_at:</strong>{{ $date(item.updated_at).format('DD/MM/YYYY HH:mm') }}
                </v-col>
                <v-col cols="12" v-if="item.active==0">
                  <v-btn 
                    small 
                    class="primary" 
                    @click.stop="aktifkan(item.id)"                                
                    :disabled="btnLoading"
                    :loading="btnLoading">
                      <v-icon>mdi-email-check</v-icon>
                      VERIFIFIKASI BUKTI BAYAR
                  </v-btn>
                </v-col>
              </td>
            </template>
            <template v-slot:no-data>
              Data belum tersedia
            </template>
          </v-data-table>
        </v-col>
      </v-row>
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
        text: 'PENDAFTAR BARU',
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
      { text: 'USERNAME', value: 'username',sortable: true },
      { text: 'EMAIL', value: 'email',sortable: true },  
      { text: 'NOMOR HP', value: 'nomor_hp',sortable: false},
      { text: 'KODE', value: 'code',sortable: false},  
      { text: 'TGL.DAFTAR', value: 'created_at',sortable: true},  
      { text: 'AKSI', value: 'actions', sortable: false,width:100 },
    ],
    expanded: [],
    search: "",
    datatable: [],

    //dialog
    dialogfrm: false,
    dialogdetailitem: false,
    data_konfirmasi:{},

    //form data   
    form_valid: true,
    daftar_jenjang: [],
    daftar_ta: [], 
    formdata: {
      name: "",
      email: "", 
      nomor_hp: "",
      username: "",
      password: "",
      kode_jenjang: "", 
      ta: "",
      created_at: '',
      updated_at: '',  
    },  
    formdefault: {
      name: "",
      email: "", 
      nomor_hp: "",
      username: "",
      password: "",
      kode_jenjang: "",
      ta: "",
      created_at: '',
      updated_at: '',   
    }, 
    editedIndex: -1,

    rule_name: [
      value => !!value||"Nama Siswa mohon untuk diisi !!!",
      value => /^[A-Za-z\s\\,\\.]*$/.test(value) || 'Nama Siswa hanya boleh string dan spasi',
    ], 
    rule_nomorhp: [
      value => !!value||"Nomor HP mohon untuk diisi !!!",
      value => /^\+[1-9]{1}[0-9]{1,14}$/.test(value) || 'Nomor HP hanya boleh angka dan gunakan kode negara didepan seperti +6281214553388',
    ], 
    rule_email: [
      value => !!value||"Email mohon untuk diisi !!!",
      v => /.+@.+\..+/.test(v) || 'Format E-mail mohon di isi dengan benar',
    ],
    rule_fakultas: [
      value => !!value||"Fakultas mohon untuk dipilih !!!"
    ], 
    rule_jenjang: [
      value => !!value||"Program studi mohon untuk dipilih !!!"
    ],   
    rule_username: [
      value => !!value||"Username mohon untuk diisi !!!"
    ], 
    rule_password: [
      value => !!value||"Password mohon untuk diisi !!!"
    ], 
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
      await this.$ajax.post('/spsb/psb',
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
      if ( item === this.expanded[0])
      {
        this.expanded=[];                
      }
      else
      {
        this.expanded=[item];
      }               
    },
    aktifkan(id)
    {
      this.btnLoading=true;
      this.$ajax.post('/akademik/kemahasiswaan/updatestatus/'+id,
        {
          'active':1
        },
        {
          headers:{
            Authorization: this.$store.getters["auth/Token"]
          }
        }
      ).then(()=>{   
        this.initialize();
        this.btnLoading = false;
      }).catch(()=>{
        this.btnLoading = false;
      });
      this.$ajax.post('/keuangan/konfirmasipembayaran/'+id,
        {
          '_method': 'put',
          'verified':1,
        },
        {
          headers:{
            Authorization: this.$store.getters["auth/Token"]
          }
        }
      ).then(({ data })=>{   
        this.data_konfirmasi = data.konfirmasi;
        this.btnLoading = false;
      }).catch(()=>{
        this.btnLoading = false;
      });
    },
    syncPermission: async function()
    {
      this.btnLoading=true;
      await this.$ajax.post('/system/users/syncallpermissions',
        {
          role_name: 'siswabaru',
          TA: this.tahun_pendaftaran, 
          kode_jenjang: this.kode_jenjang                     
        },
        {
          headers:{
            Authorization: this.$store.getters["auth/Token"]
          }
        }
      ).then(()=>{                   
        this.btnLoading = false;
      }).catch(()=>{
        this.btnLoading = false;
      });     
    },
    async addItem ()
    {
      this.daftar_ta=this.$store.getters['uiadmin/getDaftarTA'];  
      this.formdata.ta=this.tahun_pendaftaran;
      this.formdata.kode_jenjang=this.kode_jenjang;

      await this.$ajax.get('/datamaster/jenjangstudi').then(({ data })=>{
        this.daftar_jenjang = data.jenjang_studi;
      });

      this.dialogfrm = true;                       
    },
    save: async function() {
      if (this.$refs.frmdata.validate())
      {
        this.btnLoading=true;
        if (this.editedIndex > -1) 
        {
          await this.$ajax.post('/spsb/psb/updatependaftar/'+this.formdata.id,
            {
              '_method': 'PUT',
              name: this.formdata.name,
              email: this.formdata.email, 
              nomor_hp: this.formdata.nomor_hp,
              kode_jenjang: this.formdata.kode_jenjang,
              tahun_pendaftaran: this.formdata.ta,
              username: this.formdata.username,                                       
              password: this.formdata.password,  
            },
            {
              headers:{
                Authorization: this.$store.getters["auth/Token"]
              }
            }
          ).then(()=>{   
            this.initialize();
            this.closedialogfrm();
            this.btnLoading = false;
          }).catch(()=>{
            this.btnLoading = false;
          });                 
          
        } else {
          await this.$ajax.post('/spsb/psb/storependaftar',
            {
              name: this.formdata.name,
              email: this.formdata.email, 
              nomor_hp: this.formdata.nomor_hp,
              username: this.formdata.username,           
              kode_jenjang: this.formdata.kode_jenjang, 
              tahun_pendaftaran: this.formdata.ta,
              password: this.formdata.password, 
            },
            {
              headers:{
                Authorization: this.$store.getters["auth/Token"]
              }
            }
          ).then(({ data })=>{                           
            this.datatable.push(data.pendaftar);
            this.closedialogfrm();
            this.btnLoading = false;                        
          }).catch(()=>{
            this.btnLoading = false;
          });
        }
      }
    },
    async resend(id)
    {
      this.btnLoading=true;
      await this.$ajax.post('/spsb/psb/resend',
        {
          id:id, 
        },
        {
          headers:{
            Authorization: this.$store.getters["auth/Token"]
          }
        }
      ).then(()=>{           
        this.closedialogdetailitem();
        this.btnLoading = false;
      }).catch(()=>{
        this.btnLoading = false;
      });
    },
    async viewItem (item) {           
      await this.$ajax.get('/keuangan/konfirmasipembayaran/'+item.id, 
      {
        headers: {
          Authorization: this.$store.getters["auth/Token"]
        }
      }).then(({ data })=>{               
        this.formdata=item;  
        this.data_konfirmasi = data.konfirmasi;    
        this.dialogdetailitem=true;
      });          
    },
    async editItem (item) {
      this.editedIndex = this.datatable.indexOf(item);
      this.formdata = Object.assign({}, item);
      this.formdata.nomor_hp='+'+this.formdata.nomor_hp;
      this.daftar_ta=this.$store.getters['uiadmin/getDaftarTA'];              
      await this.$ajax.get('/datamaster/jenjangstudi').then(({ data })=>{
        this.daftar_jenjang = data.jenjang_studi;
      });                
      this.dialogfrm = true;
    },
    deleteItem (item) {           
      this.$root.$confirm.open('Delete', 'Apakah Anda ingin menghapus PESERTA DIDIK BARU '+item.name+' ?', { color: 'red' }).then((confirm) => {
        if (confirm)
        {
          this.btnLoading=true;
          this.$ajax.post('/spsb/psb/'+item.id,
            {
              '_method': 'DELETE',
            },
            {
              headers:{
                Authorization: this.$store.getters["auth/Token"]
              }
            }
          ).then(()=>{   
            const index = this.datatable.indexOf(item);
            this.datatable.splice(index, 1);
            this.btnLoading = false;
          }).catch(()=>{
            this.btnLoading = false;
          });
        }
      });
    },
    closedialogdetailitem () {
      this.dialogdetailitem = false;            
      setTimeout(() => {
        this.formdata = Object.assign({}, this.formdefault)
        this.editedIndex = -1;
        }, 300
      );
    },
    closedialogfrm () {
      this.dialogfrm = false;            
      setTimeout(() => {
        this.formdata = Object.assign({}, this.formdefault);                
        this.editedIndex = -1;
        this.$refs.frmdata.reset(); 
        }, 300
      );
    },
  },
  watch:{
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
  
  components:{
    SPSBLayout,
    ModuleHeader, 
    Filter7    
  },
}
</script>