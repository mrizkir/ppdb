<template>
  <div>
    <v-app-bar app class="white" elevation="0">
      <v-toolbar-title>
        <span class="hidden-sm-and-down">
          &nbsp;
        </span>
      </v-toolbar-title>
      <v-spacer />
      <v-toolbar-items class="hidden-sm-and-down">
        <v-btn to="/" class="mr-2" color="green darken-3" text large>
          BERANDA
        </v-btn>
        <v-btn to="/konfirmasipembayaran" class="mr-2" color="green darken-3" text large v-if="bukaPPDB">
          KONFIRMASI PEMBAYARAN
        </v-btn>
        <v-btn to="/login" color="green darken-3" text large>
          LOGIN FORMULIR
        </v-btn>
      </v-toolbar-items>
      <v-menu class="hidden-md-and-up" v-if="$vuetify.breakpoint.xsOnly">
        <template v-slot:activator="{ on }">
          <v-btn icon v-on="on">
            <v-icon>mdi-dots-vertical</v-icon>
          </v-btn>
        </template>
        <v-list>
          <v-list-item to="/">
            <v-list-item-title>BERANDA</v-list-item-title>
          </v-list-item>
          <v-list-item to="/psbtk">
            <v-list-item-title>PRA-PENDAFTARAN TK</v-list-item-title>
          </v-list-item>
          <v-list-item to="/psbsd">
            <v-list-item-title>PRA-PENDAFTARAN SD</v-list-item-title>
          </v-list-item>
          <v-list-item to="/psbsmp">
            <v-list-item-title>PRA-PENDAFTARAN SMP</v-list-item-title>
          </v-list-item>
          <v-list-item to="/psbsma">
            <v-list-item-title>PRA-PENDAFTARAN SMA</v-list-item-title>
          </v-list-item>
          <v-list-item to="/konfirmasipembayaran">
            <v-list-item-title>KONFIRMASI PEMBAYARAN</v-list-item-title>
          </v-list-item>
          <v-list-item to="/login">
            <v-list-item-title>LOGIN FORMULIR</v-list-item-title>
          </v-list-item>
        </v-list>
      </v-menu>
    </v-app-bar>
    <v-main>
      <slot/>
    </v-main>
    <v-footer app absolute padless class="mt-0" v-if="!hideFooter">
      <v-card flat tile class="flex">
        <v-card-text class="green darken-2 white--text text-center pa-3">
          <v-btn
            v-for="item in tautan"
            :key="item.label"
            :href="item.href"
            target="_blank"
            rel="noopener noreferrer"
            class="mx-2 my-1 white--text"
            text
          >
            <v-icon left>{{ item.icon }}</v-icon>
            {{ item.label }}
          </v-btn>
        </v-card-text>
        <v-card-text class="green darken-4 py-2 white--text text-center">
          <strong>{{ namaSekolah }}</strong>
        </v-card-text>
      </v-card>
    </v-footer>
  </div>
</template>
<script>
import { mapGetters } from "vuex";

export default {
  name: 'FrontLayout',
  created()
  {
    this.$store.dispatch('uifront/init', this.$ajax);
  },
  computed: {
    ...mapGetters("uifront", {
      namaSekolah: 'getNamaSekolah',
      bukaPPDB: "getBukaPPDB",
    }),
    hideFooter() {
      return [
        "FrontPSBtk",
        "FrontPSBtkGTK",
        "FrontPSBsd",
        "FrontPSBsdGTK",
        "FrontPSBsmp",
        "FrontPSBsmpGTK",
        "FrontPSBsma",
        "FrontPSBsmaGTK",
      ].includes(this.$route.name);
    },
  },
  data: () => ({
    tautan: [
      {
        label: "Website",
        icon: "mdi-web",
        href: "https://degreencamp.sch.id",
      },
      {
        label: "Facebook",
        icon: "mdi-facebook",
        href: "https://facebook.com/DGCislamicnaturalschool",
      },
      {
        label: "Instagram",
        icon: "mdi-instagram",
        href: "https://instagram.com/degreencamp",
      },
      {
        label: "Twitter",
        icon: "mdi-twitter",
        href: "https://twitter.com/degreencamp",
      },
      {
        label: "Telegram",
        icon: "mdi-send",
        href: "https://t.me/degreencamp",
      },
      {
        label: "Kanal SD",
        icon: "mdi-youtube",
        href: "https://youtube.com/channel/UCyoe1gF72c24zgPufv6Tt0Q",
      },
      {
        label: "Kanal TK",
        icon: "mdi-youtube",
        href: "https://youtube.com/channel/UCF9CQeGhEuPWidA1YHWP_CA",
      },
    ],
  }),
}
</script>
