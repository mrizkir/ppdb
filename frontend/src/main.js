import Vue from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'
import vuetify from './plugins/vuetify';

import api from './plugins/api';
Vue.use(api);

import '@/plugins/Dayjs';

import VueYouTubeEmbed from 'vue-youtube-embed'
Vue.use(VueYouTubeEmbed, { global: true })

Vue.config.productionTip = false

//filter output
Vue.filter('formatTA', function(value) 
{
	value = parseInt(value);
	return value+'/'+(value+1);
});
Vue.filter('formatUang', function(value) 
{
	var num = new Number(value).toFixed(2).replace(/(\d)(?=(\d{3})+\.)/g, '$1.');
	var pos = num.lastIndexOf('.');
	num = num.substring(0,pos) + ',' + num.substring(pos+1)	
	return num;
});

//mixin
Vue.mixin({
	methods:{
		
	}
})
new Vue({
	router,
	store,
	vuetify,
	render: h => h(App)
}).$mount('#app')
