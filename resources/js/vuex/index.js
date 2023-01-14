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
        setAuth(state,data){
            state.auth.authenticated = true;
            state.auth.username = data?.me?.username;

        },
        setNotAuth(state){
            state.auth.authenticated = false;
            state.auth.username = '';
        },
    }
});
