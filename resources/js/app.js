import './bootstrap';

import {createApp} from 'vue';

import App from '@/components/App.vue';
import Router from '@/router';
import Vuex from '@/vuex';
import GlobalPlugins from '@/global-plugins';

import Select2 from '@/components/UI/Select2.vue';

import Sidebar from '@/components/Sidebar.vue';
import Navbar from '@/components/Navbar.vue';

const app = createApp(App);

app.component('select2', Select2);

app.component('app-sidebar', Sidebar);
app.component('app-navbar', Navbar);

app.use(Router);
app.use(Vuex);
app.use(GlobalPlugins);

app.mount('#app');
