<template>
	<div>
		<v-system-bar app dark class="green lighten-2 white--text">
			
		</v-system-bar>	
		<v-app-bar app>
			<v-app-bar-nav-icon @click.stop="drawer = !drawer" class="grey--text"></v-app-bar-nav-icon>
			<v-toolbar-title class="headline clickable" @click.stop="$router.push('/dashboard/' + $store.getters['auth/AccessToken']).catch(err => {})">
				<span class="hidden-sm-and-down">
					FORMULIR PENDAFTARAN PPDB SEKOLAH ISLAM DE GREEN CAMP
				</span>
			</v-toolbar-title>
			<v-spacer></v-spacer>
			<v-divider
				class="mx-4"
				inset
				vertical
			></v-divider>
			<v-menu 
				:close-on-content-click="true"
				origin="center center"
				transition="scale-transition"
				:offset-y="true"
				bottom 
				left>
				<template v-slot:activator="{on}">
					<v-avatar size="30">
						<v-img :src="photoUser" v-on="on" />
					</v-avatar> 
				</template>
				<v-list>
					<v-list-item>
						<v-list-item-avatar>
							<v-img :src="photoUser"></v-img>
						</v-list-item-avatar>
						<v-list-item-content>					
							<v-list-item-title class="title">
								{{ATTRIBUTE_USER('username')}}
							</v-list-item-title>
							<v-list-item-subtitle>
								{{ROLE}}
							</v-list-item-subtitle>
						</v-list-item-content>
					</v-list-item>
					<v-divider />
					<v-list-item to="/system-users/profil">
						<v-list-item-icon class="mr-2">
							<v-icon>mdi-account</v-icon>
						</v-list-item-icon>
						<v-list-item-title>Profil</v-list-item-title>
					</v-list-item>
					<v-divider />
					<v-list-item @click.prevent="logout">
						<v-list-item-icon class="mr-2">
							<v-icon>mdi-power</v-icon>
						</v-list-item-icon>
						<v-list-item-title>Logout</v-list-item-title>
					</v-list-item>
				</v-list>
			</v-menu>			
		</v-app-bar> 
		<v-navigation-drawer v-model="drawer" width="300" dark class="green darken-1" :temporary="hideleftnav" app>
			<v-list-item>
				<v-list-item-avatar>
					<v-img :src="photoUser" @click.stop="toProfile"></v-img>
				</v-list-item-avatar>
				<v-list-item-content>					
					<v-list-item-title class="title">
						{{ATTRIBUTE_USER('username')}}
					</v-list-item-title>
					<v-list-item-subtitle>
						{{ROLE}}
					</v-list-item-subtitle>
				</v-list-item-content>
			</v-list-item>
			<v-divider></v-divider>
			<v-list expand>
				<v-list-item :to="{path: '/system-users'}" v-if="CAN_ACCESS('SYSTEM-USERS-GROUP')" link class="yellow" color="green" >
					<v-list-item-icon class="mr-2">
						<v-icon>mdi-account</v-icon>
					</v-list-item-icon>
					<v-list-item-content>
						<v-list-item-title>BOARD USERS</v-list-item-title>
					</v-list-item-content>
				</v-list-item>
				<v-list-item link v-if="CAN_ACCESS('SYSTEM-SETTING-PERMISSIONS')" to="/system-users/permissions">
					<v-list-item-icon class="mr-2">
						<v-icon>mdi-account-key</v-icon>
					</v-list-item-icon>
					<v-list-item-content>
						<v-list-item-title>
							PERMISSIONS
						</v-list-item-title>
					</v-list-item-content>
				</v-list-item>
				<v-list-item link v-if="CAN_ACCESS('SYSTEM-SETTING-ROLES')" to="/system-users/roles">
					<v-list-item-icon class="mr-2">
						<v-icon>mdi-account-group</v-icon>
					</v-list-item-icon>
					<v-list-item-content>
						<v-list-item-title>
							ROLES
						</v-list-item-title>
					</v-list-item-content>
				</v-list-item>
				<v-divider />
				<v-list-item link v-if="CAN_ACCESS('SYSTEM-USERS-SUPERADMIN_BROWSE')" to="/system-users/superadmin">
					<v-list-item-icon class="mr-2">
						<v-icon>mdi-account</v-icon>
					</v-list-item-icon>
					<v-list-item-content>
						<v-list-item-title>
							SUPERADMIN
						</v-list-item-title>
					</v-list-item-content>
				</v-list-item>
				<v-list-item link v-if="CAN_ACCESS('SYSTEM-USERS-KEUANGAN_BROWSE')" to="/system-users/keuangan">
					<v-list-item-icon class="mr-2">
						<v-icon>mdi-account</v-icon>
					</v-list-item-icon>
					<v-list-item-content>
						<v-list-item-title>
							KEUANGAN
						</v-list-item-title>
					</v-list-item-content>
				</v-list-item>
				<v-list-item link v-if="CAN_ACCESS('SYSTEM-USERS-PSB_BROWSE')" to="/system-users/psb">
					<v-list-item-icon class="mr-2">
						<v-icon>mdi-account</v-icon>
					</v-list-item-icon>
					<v-list-item-content>
						<v-list-item-title>
							TIM PSB
						</v-list-item-title>
					</v-list-item-content>
				</v-list-item>
			</v-list>
		</v-navigation-drawer> 
		<v-main class="mx-4 mb-4">			
			<slot />
		</v-main>
	</div> 
</template>
<script>
import {mapGetters} from "vuex";
export default {
	name: 'SystemUserLayout', 
	props: {
		showrightsidebar: {
			type:Boolean,
			default: true
		}
	}, 
	data:()=>({
		loginTime: 0,
		drawer:null,
	}),
	methods: {
		logout ()
		{
			this.loginTime=0;
			this.$ajax.post('/auth/logout',
				{},
				{
					headers: {
						'Authorization': this.TOKEN,
					}
				}
			).then(()=> {
				this.$store.dispatch('auth/logout');	
				this.$store.dispatch('uifront/reinit');	
				this.$store.dispatch('uiadmin/reinit');	
				this.$router.push('/');
			})
			.catch(() => {
				this.$store.dispatch('auth/logout');	
				this.$store.dispatch('uifront/reinit');	
				this.$store.dispatch('uiadmin/reinit');	
				this.$router.push('/');
			});
		},
		isBentukPT (bentuk_sekolah)
		{
			return this.$store.getters['uifront/getBentukPT']==bentuk_sekolah?true: false;
		}
	},
	computed: {
		...mapGetters('auth',{
			AUTHENTICATED: 'Authenticated',
			ACCESS_TOKEN: 'AccessToken',
			TOKEN: 'Token',
			ROLE: 'Role',
			CAN_ACCESS: 'can', 
			ATTRIBUTE_USER: 'AttributeUser',
		}),
		APP_NAME ()
		{
			return process.env.VUE_APP_NAME;
		},
		photoUser()
		{
			let img=this.ATTRIBUTE_USER('foto');
			var photo;
			if (img == '')
			{
				photo = this.$api.storageURL + '//images/users/no_photo.png';	
			}
			else
			{
				photo = this.$api.storageURL + '/' + img;	
			}
			return photo;
		},
		hideleftnav ()
		{
			if (this.$route.name== 'UsersProfil')
			{
				return true;
			}
			else
			{
				return false;
			}
		},
	},
	watch: {
		loginTime: {
			handler(value)
			{
				
				if (value >= 0)
				{
					setTimeout(() => { 
						this.loginTime=this.AUTHENTICATED == true ? this.loginTime + 1 : -1;
					}, 1000);
				}
				else
				{
					this.$store.dispatch('auth/logout');
					this.$router.replace('/login');
				}
			},
			immediate: true
		},
	}
}
</script>