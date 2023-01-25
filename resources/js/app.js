import './bootstrap';

import {createApp} from 'vue';

import App from '@/components/App.vue';
import Router from '@/router';
import Vuex from '@/vuex';
import GlobalPlugins from '@/global-plugins';

import Select2 from '@/components/UI/Select2.vue';
import Loader from '@/components/UI/Loader.vue';
import DataTable from '@/components/UI/DataTable.vue';

import Sidebar from '@/components/Sidebar.vue';
import Navbar from '@/components/Navbar.vue';

const app = createApp(App);

app.component('app-select2', Select2);
app.component('app-loader', Loader);
app.component('app-data-table', DataTable);

app.component('app-sidebar', Sidebar);
app.component('app-navbar', Navbar);

app.use(Router);
app.use(Vuex);
app.use(GlobalPlugins);

app.mount('#app');
