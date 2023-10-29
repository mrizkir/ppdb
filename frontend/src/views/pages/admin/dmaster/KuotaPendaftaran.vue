<template>
  <DataMasterLayout>
    <ModuleHeader>
      <template v-slot:icon>
        mdi-stairs-up
      </template>
      <template v-slot:name>
        KUOTA PENDAFTARAN
      </template>
      <template v-slot:subtitle>
        
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
          Halaman ini digunakan untuk mengatur kuota pendaftaran per jenjang dan tahun ajaran.
        </v-alert>
      </template>
    </ModuleHeader>
    <v-container fluid>
      <v-row class="mb-4" no-gutters>
        <v-col cols="12">
          <v-data-table
            :headers="headers"
            :items="datatable"               
            item-key="id"
            sort-by="tahun"
            show-expand            
            :expanded.sync="expanded"
            :single-expand="true"
            @click:row="dataTableRowClicked"
            class="elevation-1"
            :loading="datatableLoading"
            loading-text="Loading... Please wait"
          > 
            <template v-slot:top>
              <v-toolbar flat color="white">
                <v-toolbar-title>DAFTAR KUOTA PENADFTARAN</v-toolbar-title>
                <v-divider
                  class="mx-4"
                  inset
                  vertical
                ></v-divider>
                <v-spacer></v-spacer>
                <v-dialog v-model="dialogfrm" max-width="600px" persistent>
                  <template v-slot:activator="{ on }">
                    <v-btn color="primary" dark class="mb-2" v-on="on">TAMBAH</v-btn>
                  </template>
                  <v-form ref="frmdata" v-model="form_valid" lazy-validation>
                    <v-card>
                      <v-card-title>
                        <span class="headline">{{ formTitle }}</span>
                      </v-card-title>
                      <v-card-text>
                        <v-text-field
                          v-model="formdata.tahun"
                          label="TAHUN"
                          outlined
                          :rules="rule_tahun">
                        </v-text-field>
                        <v-text-field
                          v-model="formdata.tahun_ajaran"
                          label="TAHUN AJARAN"
                          outlined
                          :rules="rule_tahun_ajaran">
                        </v-text-field>
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
              </v-toolbar>
            </template>    
            <template v-slot:expanded-item="{ headers, item }">
              <td :colspan="headers.length" class="text-center">
                <v-col cols="12">        
                  <strong>created_at:</strong>{{ $date(item.created_at).format('DD/MM/YYYY HH:mm') }}
                  <strong>updated_at:</strong>{{ $date(item.updated_at).format('DD/MM/YYYY HH:mm') }}
                </v-col>    
              </td>
            </template>
            <template v-slot:item.actions="{ item }">
              <v-icon
                small
                class="mr-2"
                :loading="btnLoading"
                :disabled="btnLoading"
                @click.stop="editItem(item)"
              >
                mdi-pencil
              </v-icon>
            </template>
            <template v-slot:no-data>
              Data belum tersedia
            </template>
          </v-data-table>
        </v-col>
      </v-row>
    </v-container>
  </DataMasterLayout>
</template>
<script>
  import DataMasterLayout from "@/views/layouts/DataMasterLayout";
  import ModuleHeader from "@/components/ModuleHeader";
  export default {
    name: "KuotaPendaftaran",
    created() {
      this.breadcrumbs = [
        {
          text: "HOME",
          disabled: false,
          href: "/dashboard/"+this.$store.getters["auth/AccessToken"]
        },
        {
          text: "DATA MASTER",
          disabled: false,
          href: "/dmaster"
        },
        {
          text: "KUOTA PENDAFTARAN",
          disabled: true,
          href: "#"
        }
      ];
      this.initialize();
    },
    data: () => ({
      breadcrumbs: [],  

      btnLoading: false,
      datatableLoading: false,
      expanded: [],
      datatable: [],
      
      //dialog
      dialogfrm: false,

      headers: [                                            
        { text: "ID", value: "kode_jenjang",width:10, sortable: false },
        { text: "TAHUN", value: "tahun_ajaran", sortable: false},
        { text: "NAMA JENJANG", value: "nama_jenjang", sortable: false},
        { text: "KUOTA PUTRA", value: "kuota_p", sortable: false},
        { text: "KUOTA PUTRI", value: "kuota_l", sortable: false},
        { text: "AKSI", value: "actions", sortable: false, width: 100 },
      ],
      form_valid: true,
      formdata: {
        kode_jenjang: "",
        nama_jenjang: "",
        status_pendaftaran: false, 
      },  
      formdefault: {
        kode_jenjang: "",
        nama_jenjang: "",
        status_pendaftaran: false, 
      },

      editedIndex: -1,
    }),
    methods: {
      initialize: async function() {
        this.datatableLoading=true;
        await this.$ajax.get("/datamaster/kuotapendaftaran", 
        {
          headers: {
            Authorization: this.$store.getters["auth/Token"]
          }
        }).then(({ data })=>{            
          this.datatable = data.kuota;
          this.datatableLoading=false;
        });
      },
      dataTableRowClicked(item) {
        if (item === this.expanded[0]) {
          this.expanded = [];
        } else {
          this.expanded = [item];
        }
      },
      async editItem(item) {
        this.editedIndex = this.datatable.indexOf(item);
        this.formdata = Object.assign({}, item);
        this.dialogfrm = true;
      },
      save: async function() {
        if (this.$refs.frmdata.validate()) {
          this.btnLoading = true;
          if (this.editedIndex > -1) {
            await this.$ajax
              .post(
                "/datamaster/jenjangstudi/" + this.formdata.kode_jenjang,
                {
                  _method: "PUT",
                  status_pendaftaran: this.formdata.status_pendaftaran,
                },
                {
                  headers: {
                    Authorization: this.$store.getters["auth/Token"],
                  },
                }
              )
              .then(() => {
                this.initialize();
                this.closedialogfrm();
                this.btnLoading = false;
              })
              .catch(() => {
                this.btnLoading = false;
              });
          }
        }
      },
      closedialogfrm() {
        this.dialogfrm = false;
        setTimeout(() => {
          this.formdata = Object.assign({}, this.formdefault);
          this.editedIndex = -1;
          this.$refs.frmdata.reset();
        }, 300);
      },
    },
    computed: {    
      formTitle() {
        return this.editedIndex === -1 ? 'TAMBAH KUOTA' : 'UBAH KUOTA'
      },
    },
    components: {
      DataMasterLayout,
      ModuleHeader,
    },
  };
</script>
