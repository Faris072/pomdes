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
import PenerbitanTagihanTransaksi from '@/view/transaction/tagihan/Penerbitan.vue';

// pembayaran
import PembayaranTransaksi from '@/view/transaction/payment/Index.vue';

// pengiriman
import PengirimanTransaksi from '@/view/transaction/delivery/Index.vue';

//arrived
import BBMSampaiTransaksi from '@/view/transaction/arrived/Index.vue';
import BBMSampaiKetidaksesuaianTransaksi from '@/view/transaction/arrived/Discrepancy.vue';

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
                path: 'transaksi/tagihan/penerbitan/:id',
                name: 't-tagihan-penerbitan',
                component: PenerbitanTagihanTransaksi,
            },
            {
                path: 'transaksi/pembayaran',
                name: 't-pembayaran',
                component: PembayaranTransaksi,
            },
            {
                path: 'transaksi/pengiriman',
                name: 't-pengiriman',
                component: PengirimanTransaksi,
            },
            {
                path: 'transaksi/sampai',
                name: 't-sampai',
                component: BBMSampaiTransaksi,
            },
            {
                path: 'transaksi/sampai/ketidaksesuaian/:id',
                name: 't-sampai-ketidaksesuaian',
                component: BBMSampaiKetidaksesuaianTransaksi,
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
