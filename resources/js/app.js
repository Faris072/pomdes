import './bootstrap';

import {createApp} from 'vue';

import App from '@/components/App.vue';
import Router from '@/router';

// import Sidebar from '@'

const app = createApp(App);

app.use(Router);
app.component(Router);

app.mount('#app');
