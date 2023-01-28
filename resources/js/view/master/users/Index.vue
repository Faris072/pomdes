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
                                <app-data-table :table-config="tableConfig" @get-data="getDataTable">
                                    <template v-slot:body>
                                        <tr v-for="(context, index) in tableConfig?.data?.body">
                                            <td valign="middle">
                                                <center>
                                                    {{ index+1 + ((tableConfig?.config?.currentPage-1) * tableConfig?.config?.limit) }}
                                                </center>
                                            </td>
                                            <td valign="middle">
                                                <div class="wrap-profile">
                                                    {{ context?.profile?.name || '-' }}
                                                    <br>
                                                    {{ context?.username }}
                                                </div>
                                            </td>
                                            <td valign="middle">{{ context?.role?.name }}</td>
                                            <td valign="middle">
                                                <div class="wrap-empty-pusat" v-if="!context?.pusat_id">
                                                    <span class="badge badge-light-danger">Bukan Pomdes</span>
                                                </div>
                                                <div class="wrap-pusat" v-else>
                                                    {{ context?.pusat?.profile?.name }}
                                                    <br>
                                                    {{ context?.pusat?.username }}
                                                </div>
                                            </td>
                                            <td valign="middle">
                                                <div class="m-auto form-check form-switch form-check-custom form-check-solid">
                                                    <input class="m-auto form-check-input h-30px w-50px" type="checkbox" :checked="context?.is_active == 1 ? true : false" @change="switchStatus(context?.id)"/>
                                                </div>
                                            </td>
                                            <td valign="middle">
                                                <div class="dropdown m-auto d-flex justify-content-center">
                                                    <button class="btn btn-secondary dropdown-toggle btn-sm m-auto" type="button" data-bs-toggle="dropdown">Aksi</button>
                                                    <div class="dropdown-menu">
                                                        <a class="dropdown-item" href="#" style="padding:10px;">Detail</a>
                                                        <a class="dropdown-item" href="#" style="padding:10px;" @click="edit(context?.id)">Edit</a>
                                                        <a class="dropdown-item" href="#" style="padding:10px;">Hapus Sementara</a>
                                                        <a class="dropdown-item" href="#" style="padding:10px;">Hapus Permanen</a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    </template>
                                </app-data-table>
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
                                <h3 class="modal-title">{{ form?.isEdit ? 'Edit' : 'Tambah' }} User</h3>
                                <span class="text-muted">Isi form berikut untuk {{ form?.isEdit ? 'edit' : 'tambah' }} user PDOMS</span>
                            </center>
                        </div>
                        <!--begin::Close-->
                        <button type="button" class="btn-close m-2" data-bs-dismiss="modal" aria-label="Close"></button>
                        <!--end::Close-->
                    </div>

                    <div class="modal-body">
                        <div class="loading d-flex justify-content-center align-items-center" v-if="form.loading">
                            <app-loader></app-loader>
                        </div>
                        <div class="wrap-form" v-else>
                            <label for="role"><h5>Role</h5></label>
                            <app-select2 id="role" v-model="form.data.role" :options="selectList.role.list" :loading="selectList.role.loading" @get-options="getRole" placeholder="Pilih Role" :multiple="false" />
                            <br>
                            <template v-if="form?.data?.role?.id == 3">
                                <label for="pusat"><h5>Pusat</h5></label>
                                <app-select2 id="pusat" v-model="form.data.pusat" :options="selectList.pusat.list" :loading="selectList.pusat.loading" @get-options="getPusat" placeholder="Pilih Pusat" :multiple="false" />
                                <br>
                            </template>
                            <label for="username"><h5>Username</h5></label>
                            <input id="username" type="text" class="form-control" v-model="form.data.username" placeholder="Masukkan Username" autocomplete="off" />
                            <br>
                            <label for="password"><h5>Password</h5></label>
                            <input id="password" type="password" class="form-control" v-model="form.data.password" placeholder="Masukkan Password" autocomplete="off" />
                            <br>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                        <button type="button" class="btn btn-primary" @click="simpan()">Simpan</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { toDisplayString } from 'vue';

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
                    isEdit: false,
                    idEdit: 0,
                    loading: false,
                    data: {
                        role: '',
                        pusat: '',
                        username: '',
                        password: ''
                    }
                },
                tableConfig: {
                    data: {
                        header: [
                            {
                                text: 'No',
                                sort_by: 'id',
                                sort: true,
                                class: {
                                    column: '',
                                    wrap: '',
                                    text: 'text-center'
                                },
                                style: {
                                    column: 'width:10%;',
                                    wrap: '',
                                    text: '',
                                }
                            },
                            {
                                text: 'Nama',
                                sort_by: 'username',
                                sort: true,
                                class: {
                                    column: '',
                                    wrap: '',
                                    text: ''
                                },
                                style: {
                                    column: '',
                                    text: ''
                                }
                            },
                            {
                                text: 'Role',
                                sort_by: 'role_id',
                                sort: true,
                                class: {
                                    column: '',
                                    wrap: '',
                                    text: ''
                                },
                                style: {
                                    column: '',
                                    text: ''
                                }
                            },
                            {
                                text: 'Pusat',
                                sort_by: 'pusat_id',
                                sort: true,
                                class: {
                                    column: '',
                                    wrap: '',
                                    text: ''
                                },
                                style: {
                                    column: '',
                                    text: ''
                                }
                            },
                            {
                                text: 'Status',
                                sort_by: 'is_active',
                                sort: true,
                                class: {
                                    column: '',
                                    wrap: '',
                                    text: 'text-center'
                                },
                                style: {
                                    column: '',
                                    text: ''
                                }
                            },
                            {
                                text: 'Aksi',
                                sort_by: 'is_active',
                                sort: false,
                                class: {
                                    column: '',
                                    wrap: '',
                                    text: 'text-center'
                                },
                                style: {
                                    column: '',
                                    text: ''
                                }
                            },
                        ],
                        body: []
                    },
                    config: {
                        order_by: 'asc',
                        sort_by: 'id',
                        loading: false,
                        limit: 10,
                        currentPage: 1,
                        totalPage: 2,
                        search: ''
                    }
                }
            }
        },
        mounted(){
            this.getDataTable();
        },
        methods: {
            showModal(){
                this.resetModal();
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
            },
            getPusat(search, limit){
                let that = this;
                this.selectList.pusat.loading = true;
                this.$axios().get(`users/select-pusat?search=${search}&limit=${limit}`)
                    .then(res => {
                        let data = res?.data?.data;
                        this.selectList.pusat.list = [];
                        $.each(data, function(i,val){
                            that.selectList.pusat.list.push({
                                id: val?.id,
                                text: val?.profile?.name
                            });
                        });
                    })
                    .catch(err => {
                        this.$axiosHandleError(err);
                    })
                    .then(()=>{
                        this.selectList.pusat.loading = false;
                    })
            },
            getDataTable(){
                this.tableConfig.config.loading = true;

                let data = this.tableConfig?.config;
                data.page = data?.currentPage;

                this.$axios().get(`users`, {params: data})
                    .then(res => {
                        let data = res?.data?.data;

                        this.tableConfig.config.totalPage = data?.last_page;
                        this.tableConfig.config.currentPage = data?.current_page;

                        this.tableConfig.data.body = [];
                        this.tableConfig.data.body = data?.data;
                    })
                    .catch(err => {
                        this.$axiosHandleError(err);
                    })
                    .then(()=>{
                        this.tableConfig.config.loading = false;
                    });
            },
            simpan(){
                let that = this;
                let data = {
                    role_id: this.form?.data?.role?.id,
                    pusat_id: this.form?.data?.pusat?.id,
                    username: this?.form?.data?.username,
                    password: this?.form?.data?.password
                };

                this.$pageLoadingShow();
                this.$axios().post(`users`,data)
                    .then(res => {
                        $('.modal').modal('hide');
                        Swal.fire('Berhasil', 'User berhasil ditambahkan', 'success');
                        this.getDataTable();
                    })
                    .catch(err => {
                        this.$axiosHandleError(err);
                    })
                    .then(()=>{
                        this.$pageLoadingHide();
                    })
            },
            resetModal(){
                this.form = {
                    isEdit: false,
                    idEdit: 0,
                    loading: false,
                    data: {
                        role: '',
                        pusat: '',
                        username: '',
                        password: ''
                    }
                };
            },
            switchStatus(id){
                this.$pageLoadingShow();
                this.$axios().put(`users/switch-status/${id}`)
                    .then(res => {
                        toastr.success('Status berhasil diubah.');
                    })
                    .catch(err => {
                        this.$axiosHandleError(err);
                    })
                    .then(() => {
                        this.$pageLoadingHide();
                    });
            },
            edit(id){
                this.resetModal();
                $('#modal-form').modal('show');
                this.form.idEdit = id;
                this.form.loading = true;
                this.$axios().get(`users/${id}`)
                    .then(res => {
                        let data = res?.data?.data;
                        this.form.data.role = {id: data?.role?.id, text: data?.role?.name};
                        this.form.data.pusat = {id: data?.pusat?.id, text: data?.pusat?.username};
                        this.form.data.username = data?.username;
                        console.log(this.form.data);

                    })
                    .catch(err => {
                        this.$axiosHandleError(err);
                    })
                    .then(()=>{
                        this.form.loading = false;
                    });
            }
        }
    }
</script>

<style scope>
</style>
