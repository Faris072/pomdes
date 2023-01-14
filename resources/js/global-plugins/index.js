import router from '@/router';
import api from '@/global-plugins/api.js';

export default {
    install(app, options){
        const global = app.config.globalProperties;

        global.$axiosHandleError = (err) => {

        };

        global.$axios = () => {
            return api();
        };
    }
}
