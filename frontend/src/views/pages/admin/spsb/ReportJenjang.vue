<template>
    <SPSBLayout>
        <ModuleHeader>
            <template v-slot:icon>
                mdi-file-document-edit-outline
            </template>
            <template v-slot:name>
                LAPORAN PSB JENJANG STUDI
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
                        Berisi laporan formulir pendaftaran, silahkan melakukan filter tahun akademik dan program studi.
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
                                <v-toolbar-title>DAFTAR SISWA BARU</v-toolbar-title>
                                <v-divider
                                    class="mx-4"
                                    inset
                                    vertical
                                ></v-divider>
                                <v-spacer></v-spacer>
                                <v-btn 
                                    color="primary" 
                                    icon
                                    @click.stop="printtoexcel"
                                    :loading="btnLoading"
                                    :disabled="btnLoading">
                                    <v-icon>
                                        mdi-printer
                                    </v-icon>
                                </v-btn>
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
                                        <v-img :src="$api.storageURL + '/' +item.foto" />
                                    </v-avatar>
                            </v-badge>
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
    name: 'ReportJenjang', 
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
                text: 'LAPORAN PSB JENJANG STUDI',
                disabled: true,
                href: '#'
            }
        ];
        let kode_jenjang = this.$store.getters['uiadmin/getKodeJenjang'];
        this.kode_jenjang=kode_jenjang;
        this.nama_jenjang = this.$store.getters['uiadmin/getNamaJenjang'](kode_jenjang);
        this.tahun_pendaftaran=this.$store.getters['uiadmin/getTahunPendaftaran'];
        this.initialize()
    },
    data: () => ({
        firstloading: true,
        kode_jenjang: null,
        tahun_pendaftaran: null,
        nama_jenjang: null,

        dialogprofilmhsbaru: false,
        breadcrumbs: [],
        dashboard: null,

        btnLoading: false,
        datatableLoading: false,
        expanded: [],
        datatable: [],
        headers: [                        
            { text: "", value: "foto", width:70 },
            { text: 'NAMA SISWA', value: 'name', width: 350, sortable: true },
            { text: 'NOMOR HP', value: 'nomor_hp', width: 100},
            { text: 'KELAS', value: 'nkelas', width: 100, sortable: true }, 
        ],
        search: "",
        
        datamhsbaru: {}
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

                break;
                default :
                    this.datatableLoading = true;
                    await this.$ajax.post('/spsb/formulirpendaftaran',
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
            }
            this.firstloading = false;
            this.$refs.filter7.setFirstTimeLoading(this.firstloading); 
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
        printtoexcel: async function()
        {
            this.btnLoading = true;
            await this.$ajax.post('/spsb/reportspsbjenjang/printtoexcel',
                {
                    TA: this.tahun_pendaftaran,  
                    kode_jenjang: this.kode_jenjang, 
                    nama_jenjang: this.nama_jenjang, 
                },
                {
                    headers: {
                        Authorization: this.$store.getters["auth/Token"]
                    },
                    responseType: 'arraybuffer'
                }
            ).then(({ data }) => {
                const url = window.URL.createObjectURL(new Blob([data]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', 'laporan_jenjang_'+Date.now()+'.xlsx');
                link.setAttribute('id', 'download_laporan');
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                this.btnLoading = false;
            }).catch(() => {
                this.btnLoading = false;
            });
        }
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
        Filter7    
    },
}
</script>