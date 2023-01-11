<template>
    <div>
        <div class="d-flex flex-lg-row-fluid p-0 m-0" style="width:100%; height:100vh; background-color: lightblue;">
            <div style="flex:4;" id="image">
            </div>
            <div class="d-flex" style="flex:5; background-color:white; height:100%;">
                <div class="m-auto">
                    <div class="image m-auto">
                        <center>
                            <img src="@/assets/images/logo_pomdes.png" style="width:13vw;" alt="">
                        </center>
                    </div>
                    <div class="card my-5 card-login">
                        <div class="card-body">
                            <center>
                                <h1>Login PDOMS</h1>
                                <h5 class="text-muted">Pomdes Delivery Order Management System</h5>
                            </center>
                            <br>
                            <label for="username"><b>Username</b></label>
                            <input type="text" id="username" class="form-control" placeholder="Masukkan username">
                            <br>
                            <label for="password"><b>Password</b></label>
                            <input type="password" id="password" class="form-control" placeholder="Masukkan password">
                            <br>
                            <div class="form-check form-check-custom form-check-solid">
                                <input class="form-check-input" type="checkbox" value="" id="checkbox"/>
                                <label class="form-check-label" for="checkbox">
                                    <b class="text-orange">Ingat Saya</b>
                                </label>
                            </div>
                            <br>
                            <button class="btn btn-orange" style="width:100%;">Login</button>
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
        box-shadow: 0px 0px 15px rgba(255, 217, 0, 0.256);
    }
    #checkbox:checked{
        background-color: gold;
    }
</style>

