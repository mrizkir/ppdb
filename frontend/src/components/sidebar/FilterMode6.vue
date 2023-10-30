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
                v-model="tahun_ajaran"
                :items="daftar_ta"       
                label="TAHUN AJARAN"
                outlined/>
            <v-select
                v-model="semester_akademik"
                :items="daftar_semester"
                item-text="text"
                item-value="id"
                label="SEMESTER"
                outlined/>
        </v-list-item-content>
    </v-list-item>	
</template>
<script>
export default {
    name: 'FilterMode6',
    created()
    {
        this.daftar_jenjang=this.$store.getters['uiadmin/getDaftarJenjang'];
        this.kode_jenjang=this.$store.getters['uiadmin/getKodeJenjang']; 

        this.daftar_ta=this.$store.getters['uiadmin/getDaftarTA'];
        this.tahun_ajaran=this.$store.getters['uiadmin/getTahunAkademik'];
        
        this.daftar_semester=this.$store.getters['uiadmin/getDaftarSemester'];
        this.semester_akademik=this.$store.getters['uiadmin/getSemesterAkademik'];
    },
    data:()=>({
        firstloading: true,
        daftar_jenjang: [],
        kode_jenjang:null,

        daftar_ta: [],
        tahun_ajaran:null,

        daftar_semester: [],
        semester_akademik:null
    }),
    methods:{
        setFirstTimeLoading (bool)
        {
            this.firstloading=bool;
        }
    },
    watch:{
        tahun_ajaran(val)
        {
            if (!this.firstloading)
            {
                this.$store.dispatch('uiadmin/updateTahunAkademik',val);
                this.$emit('changeTahunAkademik',val);
            }            
        },
        kode_jenjang(val)
        {
            if (!this.firstloading)
            {
                this.$store.dispatch('uiadmin/updateJenjang',val);
                this.$emit('changeJenjang',val);
            }
        },
        semester_akademik(val)
        {
            if (!this.firstloading)
            {
                this.$store.dispatch('uiadmin/updateSemesterAkademik',val);
                this.$emit('changeSemesterAkademik',val);
            }
        },
    }
}
</script>