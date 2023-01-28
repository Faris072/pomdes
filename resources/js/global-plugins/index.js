import router from '@/router';
import api from '@/global-plugins/api.js';
import store from '@/vuex';

export default {
    install(app, options){
        const global = app.config.globalProperties;

        global.$axiosHandleError = (err) => {
            let data = err?.response?.data;
            let status = {
                title: 'OOPS...',
                message: 'Terjadi kesalahan',
                icon: 'error',
            };

            switch(err?.response?.status){
                case 300:
                    status.title = 'Multiple Choises';
                    status.icon = 'warning';
                    break;
                case 301:
                    status.title = 'Move Permanently';
                    status.icon = 'warning';
                    break;
                case 302:
                    status.title = 'Found';
                    status.icon = 'warning';
                    break;
                case 303:
                    status.title = 'See Other';
                    status.icon = 'warning';
                    break;
                case 304:
                    status.title = 'Not Modified';
                    status.icon = 'warning';
                    break;
                case 305:
                    status.title = 'Use Proxy';
                    status.icon = 'warning';
                    break;
                case 400:
                    status.title = 'Bad Request';
                    if(typeof data?.status?.message == 'object'){
                        status.message = ``;
                        $.each(data?.status?.message, function(index,value){
                            $.each(value, function(i,val){
                                status.message += `${val}<br>`;
                            });
                        });
                    }
                    else{
                        status.message = data?.status?.message;
                    }
                    status.icon = 'warning';
                    break;
                case 401:
                    status.title = 'Unauthorized';
                    status.message = 'Silahkan login kembali';
                    status.icon = 'warning';
                    break;
                case 403:
                    status.title = 'Forbidden';
                    status.message = data?.status?.message;
                    status.icon = 'warning';
                    break;
                case 404:
                    status.title = 'URL Not Found';
                    status.message = data?.status?.message;
                    status.icon = 'warning';
                    break;
                case 422:
                    status.title = 'Pastikan data sudah benar!';
                    $.each(data?.status?.message, function(i,val){
                        status.message = val;
                    });
                    status.icon = 'warning';
                    break;
                default:
                    status.title = 'Terjadi kesalahan koneksi';
                    status.icon = 'error';
            }
            Swal.fire({
                title: status?.title,
                html: status?.message,
                icon: status?.icon
            }).then(()=>{
                if(err?.response?.status == 401){
                    store.state.auth.authenticated = false;
                    router.push({path: '/login'});
                }
            });
        };

        global.$axios = () => {
            return api();
        };

        global.$pageLoadingShow = () => {
            store.state.pageLoading = true;
        }
        global.$pageLoadingHide = () => {
            store.state.pageLoading = false;
        }
    }
}

export function axios() {
    return api();
};

export function axiosHandleError(err){
    let data = err?.response?.data;
    let status = {
        title: 'OOPS...',
        message: 'Terjadi kesalahan',
        icon: 'error',
    };

    switch(err?.response?.status){
        case 300:
            status.title = 'Multiple Choises';
            status.icon = 'warning';
            break;
        case 301:
            status.title = 'Move Permanently';
            status.icon = 'warning';
            break;
        case 302:
            status.title = 'Found';
            status.icon = 'warning';
            break;
        case 303:
            status.title = 'See Other';
            status.icon = 'warning';
            break;
        case 304:
            status.title = 'Not Modified';
            status.icon = 'warning';
            break;
        case 305:
            status.title = 'Use Proxy';
            status.icon = 'warning';
            break;
        case 400:
            status.title = 'Bad Request';
            if(typeof data?.status?.message == 'object'){
                status.message = ``;
                $.each(data?.status?.message, function(index,value){
                    $.each(value, function(i,val){
                        status.message += `${val}<br>`;
                    });
                });
            }
            else{
                status.message = data?.status?.message;
            }
            status.icon = 'warning';
            break;
        case 401:
            status.title = 'Unauthorized';
            status.message = 'Silahkan login kembali';
            status.icon = 'warning';
            break;
        case 403:
            status.title = 'Forbidden';
            status.message = data?.status?.message;
            status.icon = 'warning';
            break;
        case 404:
            status.title = 'URL Not Found';
            status.message = data?.status?.message;
            status.icon = 'warning';
            break;
        case 422:
            status.title = 'Pastikan data sudah benar!';
            $.each(data?.status?.message, function(i,val){
                status.message = val;
            });
            status.icon = 'warning';
            break;
        default:
            status.title = 'Terjadi kesalahan koneksi';
            status.icon = 'error';
    }
    Swal.fire({
        title: status?.title,
        html: status?.message,
        icon: status?.icon
    }).then(()=>{
        if(err?.response?.status == 401){
            store.state.auth.authenticated = false;
            router.push({path: '/login'});
        }
    });
}
