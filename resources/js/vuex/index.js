import {createStore} from 'vuex';

export default createStore({
    state(){
        return {
            auth: {
                authenticated: false,
                username: '',
                name: '',
            }
        }
    },
    mutations: {

    }
});
