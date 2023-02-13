import {createRouter, createWebHistory} from 'vue-router';
import store from '@/vuex';
import {axios, axiosHandleError} from '@/global-plugins';

import Login from '@/view/Login.vue';
import Application from '@/components/Application.vue';
import NotFound from '@/components/Not-found.vue';

import Dashboard from '@/view/dashboard/Index.vue';

// master
import MasterUsers from '@/view/master/users/Index.vue';
import MasterProvince from '@/view/master/location/province/Index.vue';
import MasterCity from '@/view/master/location/city/Index.vue';
import MasterFuel from '@/view/master/fuel/Index.vue';

// transaction
// pengajuan
import PengajuanTransaksi from '@/view/transaction/pengajuan/Index.vue';
import TambahPengajuanTransaksi from '@/view/transaction/pengajuan/Tambah.vue';
import EditPengajuanTransaksi from '@/view/transaction/pengajuan/Edit.vue';

// tagihan
import TagihanTransaksi from '@/view/transaction/tagihan/Index.vue';
import TambahTagihanTransaksi from '@/view/transaction/tagihan/Tambah.vue';
import EditTagihanTransaksi from '@/view/transaction/tagihan/Edit.vue';

import dimas from '@/view/dimas/Index.vue';

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
                name: 'dashboard',
                component: Dashboard,
            },
            {
                path: 'master/users',
                name: 'm-users',
                component: MasterUsers,
            },
            {
                path: 'master/province',
                name: 'm-province',
                component: MasterProvince,
            },
            {
                path: 'master/city',
                name: 'm-city',
                component: MasterCity,
            },
            {
                path: 'master/fuel',
                name: 'm-fuel',
                component: MasterFuel,
            },
            {
                path: 'transaksi/pengajuan',
                name: 't-pengajuan',
                component: PengajuanTransaksi,
            },
            {
                path: 'transaksi/pengajuan/tambah',
                name: 't-pengajuan-tambah',
                component: TambahPengajuanTransaksi,
            },
            {
                path: 'transaksi/pengajuan/edit/:id',
                name: 't-pengajuan-edit',
                component: EditPengajuanTransaksi,
            },
            {
                path: 'transaksi/tagihan',
                name: 't-tagihan',
                component: TagihanTransaksi,
            },
            {
                path: 'transaksi/tagihan/tambah',
                name: 't-tagihan-tambah',
                component: TambahTagihanTransaksi,
            },
            {
                path: 'transaksi/tagihan/edit/:id',
                name: 't-tagihan-edit',
                component: EditTagihanTransaksi,
            },
            {
                path: 'dimas',
                name: 'c-dimas',
                component: dimas,
            },
        ]
    },
    {
        path: '/:pathMatch(.*)*',
        component: NotFound,
        name: 'not-found',
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

router.beforeEach((to, from, next) => {
    if(to.meta.auth){
        if(store.state.auth.authenticated){
            next();
        }
        else{
            axios().post('me')
                .then(res => {
                    store.commit('setAuth', res?.data?.data);
                    next();
                })
                .catch(err => {
                    axiosHandleError(err);
                })
                .then(()=>{});
        }
    }
    else{
        next();
    }
});

export default router;
