import {createRouter, createWebHistory} from 'vue-router';

import Login from '@/view/Login.vue';
import Application from '@/components/Application.vue';

import Dashboard from '@/view/dashboard/Index.vue';

let routes = [
    {
        path: '/login',
        name: 'login',
        component: Login,
        meta: {
            auth: false
        },
    },
    {
        path: '/',
        name: 'application',
        component: Application,
        meta: {
            auth: true
        },
        children: [
            {
                path: '',
                name: 'Dashboard',
                component: Dashboard,
            }
        ]
    },
];

let router = createRouter({
    history: createWebHistory(),
    routes,
    scrollBehavior(to, from, savedPosition) {
        return {
            top: 0
        }
    }
});


export default router;
