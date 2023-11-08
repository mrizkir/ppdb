<template>
  <v-list-item>
    <v-list-item-content>
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
    this.daftar_ta=this.$store.getters['uiadmin/getDaftarTA'];
    this.tahun_ajaran=this.$store.getters['uiadmin/getTahunAjaran'];
    
    this.daftar_semester=this.$store.getters['uiadmin/getDaftarSemester'];
    this.semester_akademik=this.$store.getters['uiadmin/getSemester'];
  },
  data:()=>({
    firstloading: true,
    
    daftar_ta: [],
    tahun_ajaran:null,

    daftar_semester: [],
    semester_akademik:null
  }),
  methods: {
    setFirstTimeLoading (bool)
    {
      this.firstloading=bool;
    }
  },
  watch: {
    tahun_ajaran(val)
    {
      if (!this.firstloading)
      {
        this.$store.dispatch('uiadmin/updateTahunAjaran',val);
        this.$emit('changeTahunAjaran',val);
      }            
    },
    semester_akademik(val)
    {
      if (!this.firstloading)
      {
        this.$store.dispatch('uiadmin/updateSemester',val); 
        this.$emit('changeSemester',val);
      }
    },
  }
}
</script>