<template>
    <div>
        <div id="main-content">
            <div class="post d-flex flex-column-fluid" id="kt_post">
                <div id="kt_content_container" class="container-xxl">
                    <div class="card card-flush mt-5 mb-5 mb-xl-10" id="kt_profile_details_view">
                        <div class="card card-xl-stretch mb-5 mb-xl-8">
                            <div class="card-header border-0 pt-5 align-items-center" style="justify-content:flex-end;">
                                <button class="btn btn-warning" @click="showModal()">Tambah Data</button>
                            </div>
                            <div class="card-body pt-5">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" tabindex="-1" id="modal-form">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="m-auto" style="width:100%;">
                            <center>
                                <h3 class="modal-title">Tambah User</h3>
                                <span class="text-muted">Isi form berikut untuk tambah user PDOMS</span>
                            </center>
                        </div>
                        <!--begin::Close-->
                        <button type="button" class="btn-close m-2" data-bs-dismiss="modal" aria-label="Close"></button>
                        <!--end::Close-->
                    </div>

                    <div class="modal-body">
                        <div class="wrap-form">
                            <label for="role"><h5>Role</h5></label>
                            <select2 id="role" v-model="form.data.role" :options="selectList.role.list" :loading="selectList.role.loading" @get-options="getRole" placeholder="Pilih Role" :multiple="false" />
                            <br>
                            <template v-if="form?.data?.role?.id == 3">
                                <label for="pusat"><h5>Pusat</h5></label>
                                <select2 id="pusat" v-model="form.data.pusat" :options="selectList.pusat.list" :loading="selectList.pusat.loading" @get-options="getPusat" placeholder="Pilih Pusat" :multiple="false" />
                                <br>
                            </template>
                            <label for="username"><h5>Username</h5></label>
                            <input id="username" type="text" class="form-control" v-model="form.data.username" placeholder="Masukkan Username" />
                            <br>
                            <label for="password"><h5>Password</h5></label>
                            <input id="password" type="password" class="form-control" v-model="form.data.password" placeholder="Masukkan Password" />
                            <br>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                        <button type="button" class="btn btn-primary" @click="updateProfile()">Simpan Perubahan</button>
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
                selectList: {
                    role: {
                        loading: false,
                        list: []
                    },
                    pusat: {
                        loading: false,
                        list: []
                    },
                },
                form: {
                    isEdit: 0,
                    data: {
                        role: '',
                        pusat: '',
                        username: '',
                        password: ''
                    }
                }
            }
        },
        mounted(){

        },
        methods: {
            showModal(){
                $('#modal-form').modal('show');
            },
            getRole(){
                let that = this;
                this.selectList.role.loading = true;
                this.$axios().get(`role`)
                    .then(res => {
                        let data = res?.data?.data;
                        this.selectList.role.list = [];
                        $.each(data, function(i,val){
                            that.selectList.role.list.push({id: val?.id, text: val?.name});
                        });
                    })
                    .catch(err => {
                        this.$axiosHandleError(err);
                    })
                    .then(() => {
                        this.selectList.role.loading = false;
                    });
            }
        }
    }
</script>

<style scope>
</style>
