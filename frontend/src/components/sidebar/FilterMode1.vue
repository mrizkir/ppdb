<template>
  <v-list-item>
    <v-list-item-content>
      <v-select
        v-model="tahun_ajaran"
        :items="daftar_ta"
        label="TAHUN AJARAN"
        outlined/>
    </v-list-item-content>
  </v-list-item>	
</template>
<script>
export default {
  name: 'FilterMode1',
  created()
  {
    this.daftar_ta=this.$store.getters['uiadmin/getDaftarTA'];
    this.tahun_ajaran=this.$store.getters['uiadmin/getTahunAjaran'];
  },
  data:()=>({
    firstloading: true,
    
    daftar_ta: [],
    tahun_ajaran:null
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
  }
}
</script>