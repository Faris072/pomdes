<template>
    <div>
        <div class="d-flex flex-lg-row-fluid p-0 m-0" style="width:100%; height:100vh; background-color: lightblue;">
            <div style="flex:4;" id="image">
                <div class="cover d-flex" style="background-image:linear-gradient(orange, transparent); width:100%; height:30%; align-items:flex-end;">
                    <div class="container">
                        <img src="@/assets/images/logo_pomdes.png" alt="" style="width:60px;">
                        <br><br>
                        <h1 style="color:white;"><b>PT ZOOMINDO INTI PERKASA TBK</b></h1>
                        <span style="color:white;">Pomdes Delivery Order Management System (PDOMS)</span>
                    </div>
                </div>
            </div>
            <div class="d-flex" style="flex:5; background-color:white; height:100%;">
                <div class="m-auto">
                    <div class="image m-auto">
                        <center>
                            <img src="@/assets/images/logo_pomdes.png" style="width:12vw;" alt="">
                        </center>
                    </div>
                    <div class="card my-5 card-login">
                        <div class="card-body">
                            <center>
                                <h2><b>Login PDOMS</b></h2>
                                <h5 class="text-muted">Pomdes Delivery Order Management System</h5>
                            </center>
                            <br>
                            <label for="username"><b>Username</b></label>
                            <input type="text" v-model="username" id="username" class="form-control" placeholder="Masukkan username" required>
                            <br>
                            <label for="password"><b>Password</b></label>
                            <input type="password" v-model="password" id="password" class="form-control" placeholder="Masukkan password" required>
                            <br>
                            <div class="form-check form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" value="" id="checkbox"/>
                                <label class="form-check-label" for="checkbox">
                                    <b class="text-orange">Ingat Saya</b>
                                </label>
                            </div>
                            <br>
                            <button class="btn btn-orange" style="width:100%;" @click="login()">Login</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data(){
            return {
                username: '',
                password: '',
                validator: {
                    form: {
                        username: false,
                        password: false
                    },
                    touch: false,
                }
            }
        },
        methods: {
            login(){
                let data = {
                    username: this.username,
                    password: this.password
                };
                this.$axios().post('login',data)
                    .then(res => {
                        let data = res?.data?.data;
                        console.log(data);
                        console.log(this.$store.state.auth);
                        this.$store.commit('setAuth',data);
                        console.log(this.$store.state.auth);
                    })
                    .catch(err => {
                        console.log(err);
                    })
                    .then(() => {

                    });
            }
        }
    }
</script>

<style scoped>
    #image{
        background-image: url('@/assets/images/pomdes.jpeg');
        background-repeat: no-repeat;
        background-size: cover;
        object-fit: cover;
        background-position-x: center;
        height:100%;
        width:100%;
    }
    .card-login{
        border-radius:5px;
        box-shadow: 0px 0px 15px rgba(132, 122, 71, 0.256);
    }
    #checkbox:checked{
        background-color: gold;
    }
</style>

