<template>
    <v-list-item>
        <v-list-item-content>
            <v-select
                v-model="kode_jenjang"
                :items="daftar_jenjang"
                item-text="text"
                item-value="id"
                label="JENJANG STUDI"
                outlined/>
            <v-select
                v-model="tahun_pendaftaran"
                :items="daftar_ta"
                label="TAHUN PENDAFTARAN"
                outlined/>
        </v-list-item-content>
    </v-list-item>	
</template>
<script>
export default {
    name: 'FilterMode7',
    props: {
        halaman: {
            type: String,
            default: "",
        },
    },
    created()
    {
        this.syncFromStore();
    },
    data:()=>({
        firstloading: true,
        kode_jenjang:null,
        tahun_pendaftaran:null
    }),
    methods: {
        setFirstTimeLoading (bool)
        {
            this.firstloading=bool;
        },
        syncFromStore()
        {
            if (this.halaman) {
                const filter = this.$store.getters['uiadmin/getFilterHalaman'](this.halaman);
                if (filter) {
                    this.kode_jenjang = filter.kode_jenjang;
                    this.tahun_pendaftaran = filter.tahun_pendaftaran;
                    return;
                }
            }
            this.kode_jenjang=this.$store.getters['uiadmin/getKodeJenjang'];
            this.tahun_pendaftaran=this.$store.getters['uiadmin/getTahunPendaftaran'];
        },
        simpanFilterHalaman()
        {
            if (!this.halaman) {
                return;
            }
            this.$store.dispatch('uiadmin/updateFilterHalaman', {
                name: this.halaman,
                tahun_pendaftaran: this.tahun_pendaftaran,
                kode_jenjang: this.kode_jenjang,
            });
        },
    },
    computed: {
        daftar_jenjang() {
            return this.$store.getters['uiadmin/getDaftarJenjang'];
        },
        daftar_ta() {
            return this.$store.getters['uiadmin/getDaftarTA'];
        },
    },
    watch: {
        '$store.state.uiadmin.loaded'(loaded)
        {
            if (loaded) {
                this.syncFromStore();
            }
        },
        tahun_pendaftaran(val)
        {
            if (!this.firstloading)
            {
                if (this.halaman) {
                    this.simpanFilterHalaman();
                } else {
                    this.$store.dispatch('uiadmin/updateTahunPendaftaran',val);
                }
                this.$emit('changeTahunPendaftaran',val);
            }
        },
        kode_jenjang(val)
        {
            if (!this.firstloading)
            {
                if (this.halaman) {
                    this.simpanFilterHalaman();
                } else {
                    this.$store.dispatch('uiadmin/updateJenjang',val);
                }
                this.$emit('changeJenjang',val);
            }
        },
    }
}
</script>
