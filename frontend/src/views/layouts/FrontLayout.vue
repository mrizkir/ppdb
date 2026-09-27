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
				<v-card-text class="green darken-2 white--text text-center pa-5 hidden-sm-and-down">
					<v-btn
						v-for="icon in icons"
						:key="icon"
						class="mx-8 white--text"
						icon
					>
						<v-icon size="45px">{{ icon }}</v-icon>
					</v-btn>
				</v-card-text>
				<v-card-text class="green darken-2 white--text text-center pa-2 hidden-lg-and-up">
					<v-btn
						v-for="icon in icons"
						:key="icon"
						class="mx-4 white--text"
						icon
					>
						<v-icon size="30px">{{ icon }}</v-icon>
					</v-btn>
				</v-card-text>
				<v-card-text class="green darken-4 py-2 white--text text-center">
					<strong>{{ namaSekolahAlias }}</strong>
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
			namaSekolahAlias: 'getNamaSekolahAlias',
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
		icons: [
			"mdi-github",
			"mdi-twitter",
			"mdi-linkedin",
			"mdi-instagram",
		],
	}),
}
</script>
