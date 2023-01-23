import {createStore} from 'vuex';

export default createStore({
    state(){
        return {
            auth: {
                authenticated: false,
                username: '',
                name: '',
            },
            pageLoading: false
        }
    },
    mutations: {
        setAuth(state,data){
            state.auth = data?.me;
            state.auth.authenticated = true;
        },
        setNoAuth(state){
            state.auth = {};
            state.auth.authenticated = false;
        },
        pageLoadingShow(state){
            state.pageLoading = true;
        },
        pageLoadingShow(state){
            state.pageLoading = false;
        }
    }
});
