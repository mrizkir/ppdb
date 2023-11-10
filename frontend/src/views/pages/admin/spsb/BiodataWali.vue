<template>
  <SPSBLayout>
    <ModuleHeader v-if="dashboard!='siswabaru'">
      <template v-slot:icon>
        mdi-file-document-edit-outline
      </template>
      <template v-slot:name>
        BIODATA WALI
      </template>
      <template v-slot:subtitle>
        TAHUN PENDAFTARAN {{ tahun_pendaftaran }} - {{ nama_jenjang }}
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
            Berisi kelengkapan biodata ananda, silahkan melakukan filter tahun akademik dan program studi.
        </v-alert>
      </template>
    </ModuleHeader> 
    <v-container fluid v-if="dashboard== 'siswabaru' || datapesertadidik!=null">
      <FormBiodataWali :user_id="user_id" />
    </v-container>
    <v-container fluid v-else>
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
                <v-toolbar-title>DAFTAR SISWA BARU</v-toolbar-title>
                <v-divider
                  class="mx-4"
                  inset
                  vertical
                ></v-divider>
                <v-spacer></v-spacer>
              </v-toolbar>
            </template>
            <template v-slot:item.foto="{ item }">
              <v-badge
                  bordered
                  :color="badgeColor(item)"
                  :icon="badgeIcon(item)"
                  overlap
                > 
                  <v-avatar size="30">
                    <v-img :src="$api.url+'/'+item.foto" />
                  </v-avatar>
              </v-badge>
            </template>
            <template v-slot:item.actions="{ item }">
              <v-icon
                small
                class="mr-2"
                @click.stop="editItem(item)">
                mdi-pencil
              </v-icon>
            </template>
            <template v-slot:expanded-item="{ headers, item }">
              <td :colspan="headers.length" class="text-center">
                <v-col cols="12">
                  <strong>ID:</strong>{{ item.id }}
                  <strong>created_at:</strong>{{ $date(item.created_at).format('DD/MM/YYYY HH:mm') }}
                  <strong>updated_at:</strong>{{ $date(item.updated_at).format('DD/MM/YYYY HH:mm') }}
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
    <template v-slot:filtersidebar v-if="dashboard!='siswabaru'">
      <Filter7 v-on:changeTahunPendaftaran="changeTahunPendaftaran" v-on:changeJenjang="changeJenjang" ref="filter7" />
    </template>
  </SPSBLayout>
</template>
<script>
  import SPSBLayout from '@/views/layouts/SPSBLayout';
  import ModuleHeader from '@/components/ModuleHeader';
  import FormBiodataWali from '@/components/FormBiodataWali';
  import Filter7 from '@/components/sidebar/FilterMode7';
  export default {
    name: 'BiodataWali', 
    created() {
      this.dashboard = this.$store.getters['uiadmin/getDefaultDashboard'];
      this.breadcrumbs = [
        {
          text: 'HOME',
          disabled: false,
          href: '/dashboard/' + this.$store.getters['auth/AccessToken']
        },
        {
          text: 'SPSB',
          disabled: false,
          href: '/spsb'
        },
        {
          text: 'BIODATA WALI',
          disabled: true,
          href: '#'
        }
      ];
      this.breadcrumbs[1].disabled=(this.dashboard== 'siswabaru'||this.dashboard== 'mahasiswa');
      
      let kode_jenjang=this.$store.getters['uiadmin/getKodeJenjang'];
      this.kode_jenjang=kode_jenjang;
      this.nama_jenjang = this.$store.getters['uiadmin/getNamaJenjang'](kode_jenjang);
      this.tahun_pendaftaran=this.$store.getters['uiadmin/getTahunPendaftaran'];
      this.initialize()
    },
    data: () => ({
      user_id: null,
      firstloading: true,
      kode_jenjang: null,
      tahun_pendaftaran: null,
      nama_jenjang: null,

      breadcrumbs: [],
      dashboard: null,

      btnLoading: false,
      datatableLoading: false,
      expanded: [],
      datatable: [],
      headers: [                        
        { text: '', value: 'foto', width:70 },
        { text: 'NAMA SISWA', value: 'name', width: 350, sortable: true },
        { text: 'NAMA WALI', value: 'nama_wali'}, 
        { text: 'NOMOR HP', value: 'nomor_hp', width: 100}, 
        { text: 'AKSI', value: 'actions', sortable: false, width:50 },
      ],
      search: "",
      
      datapesertadidik: null
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
        switch(this.dashboard)
        {
          case 'siswabaru':
            this.user_id=this.$store.getters['auth/AttributeUser']('id');
          break;
          default :
            this.datatableLoading = true;
            await this.$ajax.post('/spsb/biodatawali',
            {
              TA: this.tahun_pendaftaran,
              kode_jenjang: this.kode_jenjang,
            },
            {
              headers: {
                Authorization: this.$store.getters["auth/Token"]
              }
            }).then(({ data }) => {
              this.datatable = data.psb;
              this.datatableLoading = false;
            }); 
            this.firstloading=false;
            this.$refs.filter7.setFirstTimeLoading(this.firstloading); 
        }
        
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
      badgeColor(item)
      {
        return item.active == 1 ? 'success': 'error'
      },
      badgeIcon(item)
      {
        return item.active == 1 ? 'mdi-check-bold': 'mdi-close-thick'
      },
      editItem(item)
      {
        this.user_id = item.id;
        this.datapesertadidik = item;
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
          this.nama_jenjang = this.$store.getters['uiadmin/getNamaJenjang'](val);
          this.initialize();
        }
      }
    },
    components: {
      SPSBLayout,
      ModuleHeader,
      FormBiodataWali,
      Filter7    
    },
  }
</script>
