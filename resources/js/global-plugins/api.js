import axios from 'axios';

export default (isFormData = false) => {
    const _token = localStorage.getItem('pomdes_token');

    let header = {
        Authorization: _token ? 'Bearer ' + _token : '',
    };

    let headerFormData = {
        Authorization: _token ? 'Bearer ' + _token : '',
        "Content-Type": "multipart/form-data",
    };

    let instance =  axios.create({
        baseURL: urlApi,
        headers: isFormData == false ? header : headerFormData,
    });

    // before a request is made start the nprogress
    instance.interceptors.request.use(config => {
        // NProgress.start()
        return config
    })

    // before a response is returned stop nprogress
    instance.interceptors.response.use(response => {
        // NProgress.done()
        return response
    })

    return instance
}
