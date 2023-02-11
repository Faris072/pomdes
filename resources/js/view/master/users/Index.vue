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
                                                    <b>{{ context?.profile?.name || '-' }}</b>
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
                                                    <b>{{ context?.pusat?.profile?.name }}</b>
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
                                                        <a class="dropdown-item" href="#" style="padding:10px;" @click="showReject(context?.id)">Approve</a>
                                                        <a class="dropdown-item" href="#" style="padding:10px;" @click="getDetail(context?.id)">Detail</a>
                                                        <a class="dropdown-item" href="#" style="padding:10px;" @click="edit(context?.id)">Edit</a>
                                                        <a class="dropdown-item" href="#" style="padding:10px;" @click="resetPassword(context?.id)">Reset Password</a>
                                                        <a class="dropdown-item" href="#" style="padding:10px;" @click="hapus(context.id)">Hapus</a>
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
                            <template v-if="!form.isEdit">
                                <label for="password"><h5>Password</h5></label>
                                <input id="password" type="password" class="form-control" v-model="form.data.password" placeholder="Masukkan Password" autocomplete="off" />
                            </template>
                            <br>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                        <button type="button" class="btn btn-primary" v-if="!form.isEdit" @click="simpan()">Simpan</button>
                        <button type="button" class="btn btn-primary" v-else @click="update()">Update</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" tabindex="-1" id="modal-detail">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="m-auto" style="width:100%;">
                            <center>
                                <h3 class="modal-title">Detail User</h3>
                                <span class="text-muted">Berikut adalah detail user PDOMS</span>
                            </center>
                        </div>
                        <!--begin::Close-->
                        <button type="button" class="btn-close m-2" data-bs-dismiss="modal" aria-label="Close"></button>
                        <!--end::Close-->
                    </div>

                    <div class="modal-body" style="height:93vh; overflow-x:auto;">
                        <div class="loading d-flex justify-content-center align-items-center" style="height:100%;" v-if="detail.loading">
                            <app-loader></app-loader>
                        </div>
                        <div class="wrap-detail" v-else>
                            <div class="foto my-3">
                                <center id="wrap-foto-master-profile"></center>
                            </div>
                            <br>
                            <div class="info-profile container">
                                <div class="row my-3">
                                    <div class="col-md-4">
                                        <h5 class="text-muted">Role</h5>
                                    </div>
                                    <div class="col-md-8">
                                        <h5>{{ detail.data?.role?.name || '-' }}</h5>
                                    </div>
                                </div>
                                <div class="row my-3">
                                    <div class="col-md-4">
                                        <h5 class="text-muted">Username</h5>
                                    </div>
                                    <div class="col-md-8">
                                        <h5>{{ detail.data?.username }}</h5>
                                    </div>
                                </div>
                                <div class="row my-3">
                                    <div class="col-md-4">
                                        <h5 class="text-muted">Nama</h5>
                                    </div>
                                    <div class="col-md-8">
                                        <h5>{{ detail.data?.profile?.name || '-' }}</h5>
                                    </div>
                                </div>
                                <div class="row my-3">
                                    <div class="col-md-4">
                                        <h5 class="text-muted">Nomor Telpon</h5>
                                    </div>
                                    <div class="col-md-8">
                                        <h5>{{ detail.data?.profile?.phone || '-' }}</h5>
                                    </div>
                                </div>
                                <div class="row my-3">
                                    <div class="col-md-4">
                                        <h5 class="text-muted">Email</h5>
                                    </div>
                                    <div class="col-md-8">
                                        <h5>{{ detail.data?.profile?.email || '-' }}</h5>
                                    </div>
                                </div>
                                <div class="row my-3">
                                    <div class="col-md-4">
                                        <h5 class="text-muted">Lokasi</h5>
                                    </div>
                                    <div class="col-md-8">
                                        <h5>{{ detail.data?.profile?.city?.name || '-' }}, {{ detail.data?.profile?.city?.province?.name || '-' }}</h5>
                                    </div>
                                </div>
                                <div class="row my-3">
                                    <div class="col-md-4">
                                        <h5 class="text-muted">Tanggal Bergabung</h5>
                                    </div>
                                    <div class="col-md-8">
                                        <h5>{{ $moment(detail.data?.created_at).format('DD-MM-YYYY H:m') || '-' }}</h5>
                                    </div>
                                </div>
                                <br>
                                <!--begin::Accordion-->
                                <div class="accordion" id="accordion-pomdes" v-if="detail?.data?.role_id == 2">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="kt_accordion_1_header_1">
                                            <button class="accordion-button fs-4 fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#accordion-pomdes-content" aria-expanded="true" aria-controls="accordion-pomdes">Pomdes</button>
                                        </h2>
                                        <div id="accordion-pomdes-content" class="accordion-collapse collapse show" aria-labelledby="kt_accordion_1_header_1" data-bs-parent="#accordion-pomdes">
                                            <div class="accordion-body">
                                                <div class="table-pomdes" style="overflow:auto;">
                                                    <table class="table table-bordered" style="border:1px solid black;">
                                                        <thead style="background-color:#F5F8FA !important;">
                                                            <tr>
                                                                <th class="text-center"><b>No</b></th>
                                                                <th><b>Username</b></th>
                                                                <th><b>Nama</b></th>
                                                                <th><b>Lokasi</b></th>
                                                                <th class="text-center"><b>Status</b></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr v-for="(context,index) in detail?.data?.pomdes">
                                                                <td class="text-center">{{ index+1 }}</td>
                                                                <td><a href="javascript:;" @click="getDetailPomdes(context?.id)">{{ context?.username }}</a></td>
                                                                <td>{{ context?.profile?.name || '-' }}</td>
                                                                <td>{{ context?.profile?.city?.name || '-' }}, {{ context?.profile?.city?.province?.name || '-' }}</td>
                                                                <td class="text-center">
                                                                    <span class="badge badge-light-success" v-if="context?.is_active == 1">Aktif</span>
                                                                    <span class="badge badge-light-danger" v-else>Tidak Aktif</span>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Accordion-->
                                <!--begin::Accordion-->
                                <div class="accordion" id="accordion-pusat" v-if="detail?.data?.role_id == 3">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="kt_accordion_1_header_1">
                                            <button class="accordion-button fs-4 fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#accordion-pusat-content" aria-expanded="true" aria-controls="accordion-pusat">Pusat</button>
                                        </h2>
                                        <div id="accordion-pusat-content" class="accordion-collapse collapse show" aria-labelledby="kt_accordion_1_header_1" data-bs-parent="#accordion-pusat">
                                            <div class="accordion-body">
                                                <div class="row my-3">
                                                    <div class="col-md-4">
                                                        <h5 class="text-muted">Username</h5>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <h5>{{ detail.data?.pusat?.username }}</h5>
                                                    </div>
                                                </div>
                                                <div class="row my-3">
                                                    <div class="col-md-4">
                                                        <h5 class="text-muted">Nama</h5>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <h5>{{ detail.data?.pusat?.profile?.name || '-' }}</h5>
                                                    </div>
                                                </div>
                                                <div class="row my-3">
                                                    <div class="col-md-4">
                                                        <h5 class="text-muted">Nomor Telpon</h5>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <h5>{{ detail.data?.pusat?.profile?.phone || '-' }}</h5>
                                                    </div>
                                                </div>
                                                <div class="row my-3">
                                                    <div class="col-md-4">
                                                        <h5 class="text-muted">Email</h5>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <h5>{{ detail.data?.pusat?.profile?.email || '-' }}</h5>
                                                    </div>
                                                </div>
                                                <div class="row my-3">
                                                    <div class="col-md-4">
                                                        <h5 class="text-muted">Lokasi</h5>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <h5>{{ detail.data?.pusat?.profile?.city?.name || '-' }}, {{ detail.data?.pusat?.profile?.city?.province?.name || '-' }}</h5>
                                                    </div>
                                                </div>
                                                <div class="row my-3">
                                                    <div class="col-md-4">
                                                        <h5 class="text-muted">Tanggal Bergabung</h5>
                                                    </div>
                                                    <div class="col-md-8">
                                                        <h5>{{ $moment(detail.data?.pusat?.created_at).format('DD-MM-YYYY H:m') || '-' }}</h5>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <!--end::Accordion-->
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" tabindex="-1" id="modal-pomdes">
            <div class="modal-dialog modal-lg" style="border:1px solid lightgray;">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="m-auto" style="width:100%;">
                            <center>
                                <h3 class="modal-title">Detail User Pomdes</h3>
                                <span class="text-muted">Berikut adalah detail user role pomdes PDOMS</span>
                            </center>
                        </div>
                        <!--begin::Close-->
                        <button type="button" class="btn-close m-2" data-bs-dismiss="modal" aria-label="Close"></button>
                        <!--end::Close-->
                    </div>

                    <div class="modal-body" style="height:93vh; overflow-x:auto;">
                        <div class="loading d-flex justify-content-center align-items-center" style="height:100%;" v-if="detail.loadingPomdes">
                            <app-loader></app-loader>
                        </div>
                        <div class="wrap-detail" v-else>
                            <div class="foto my-3">
                                <center id="wrap-foto-master-profile-pomdes"></center>
                            </div>
                            <br>
                            <div class="info-profile container">
                                <div class="row my-3">
                                    <div class="col-md-4">
                                        <h5 class="text-muted">Role</h5>
                                    </div>
                                    <div class="col-md-8">
                                        <h5>{{ detail.dataPomdes?.role?.name || '-' }}</h5>
                                    </div>
                                </div>
                                <div class="row my-3">
                                    <div class="col-md-4">
                                        <h5 class="text-muted">Username</h5>
                                    </div>
                                    <div class="col-md-8">
                                        <h5>{{ detail.dataPomdes?.username }}</h5>
                                    </div>
                                </div>
                                <div class="row my-3">
                                    <div class="col-md-4">
                                        <h5 class="text-muted">Nama</h5>
                                    </div>
                                    <div class="col-md-8">
                                        <h5>{{ detail.dataPomdes?.profile?.name || '-' }}</h5>
                                    </div>
                                </div>
                                <div class="row my-3">
                                    <div class="col-md-4">
                                        <h5 class="text-muted">Nomor Telpon</h5>
                                    </div>
                                    <div class="col-md-8">
                                        <h5>{{ detail.dataPomdes?.profile?.phone || '-' }}</h5>
                                    </div>
                                </div>
                                <div class="row my-3">
                                    <div class="col-md-4">
                                        <h5 class="text-muted">Email</h5>
                                    </div>
                                    <div class="col-md-8">
                                        <h5>{{ detail.dataPomdes?.profile?.email || '-' }}</h5>
                                    </div>
                                </div>
                                <div class="row my-3">
                                    <div class="col-md-4">
                                        <h5 class="text-muted">Lokasi</h5>
                                    </div>
                                    <div class="col-md-8">
                                        <h5>{{ detail.dataPomdes?.profile?.city?.name || '-' }}, {{ detail.dataPomdes?.profile?.city?.province?.name || '-' }}</h5>
                                    </div>
                                </div>
                                <div class="row my-3">
                                    <div class="col-md-4">
                                        <h5 class="text-muted">Tanggal Bergabung</h5>
                                    </div>
                                    <div class="col-md-8">
                                        <h5>{{ $moment(detail.dataPomdes?.created_at).format('DD-MM-YYYY H:m') || '-' }}</h5>
                                    </div>
                                </div>
                                <br>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
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
                token: localStorage.getItem('pomdes_token'),
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
                reject: {
                    data: {
                        description:'',
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
                },
                detail: {
                    loading: false,
                    loadingPomdes: false,
                    data: '',
                    dataPomdes: '',
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
                                text: val?.username
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
                this.form.isEdit = true;
                this.form.idEdit = id;
                this.form.loading = true;
                this.$axios().get(`users/${id}`)
                    .then(res => {
                        let data = res?.data?.data;
                        this.form.data.role = {id: data?.role?.id, text: data?.role?.name};
                        this.form.data.pusat = {id: data?.pusat?.id, text: data?.pusat?.username};
                        this.form.data.username = data?.username;

                    })
                    .catch(err => {
                        this.$axiosHandleError(err);
                    })
                    .then(()=>{
                        this.form.loading = false;
                    });
            },
            update(){
                this.$pageLoadingShow();

                let data = {
                    role_id: this.form?.data?.role?.id,
                    pusat_id: this.form?.data?.pusat?.id,
                    username: this?.form?.data?.username,
                };


                this.$axios().put(`users/${this.form.idEdit}`, data)
                    .then(res => {
                        $('.modal').modal('hide');
                        Swal.fire('Berhasil','Data user berhasil diperbarui','success');
                        this.getDataTable();
                    })
                    .catch(err => {
                        this.$axiosHandleError(err);
                    })
                    .then(()=>{
                        this.$pageLoadingHide();
                    });
            },
            resetPassword(id){
                Swal.fire({
                    title: `Reset password untuk user ini?`,
                    html: `Password akan direset ke <b>${import.meta.env.VITE_DEFAULT_PASSWORD}</b>`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Reset',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#DB3700'
                }).then(result => {
                    if(result.isConfirmed){
                        this.$pageLoadingShow();
                        this.$axios().put(`users/reset-password/${id}`)
                            .then(res => {
                                Swal.fire('Berhasil', 'Password berhasil diperbarui', 'success');
                                this.getDataTable();
                            })
                            .catch(err => {
                                this.$axiosHandleError(err);
                            })
                            .then(() => {
                                this.$pageLoadingHide();
                            });
                    }
                });
            },
            getDetail(id){
                let that = this;
                this.resetModal();
                $('#modal-detail').modal('show');
                this.detail.loading = true;
                this.detail.data = '';
                this.$axios().get(`users/${id}`)
                    .then(res => {
                        this.detail.loading = false;
                        let data = res?.data?.data;
                        console.log(data);
                        this.detail.data = data;
                        setTimeout(function(){
                            $(`#wrap-foto-master-profile`).html(`<img src="${data?.profile?.photo_profile?.link+'?token='+that.token}" style="width:150px; height:150px; object-fit: cover; border-radius:100%; border:1px solid black;">`);
                        },300);
                    })
                    .catch(err => {
                        this.$axiosHandleError(err);
                    })
                    .then(()=>{
                        this.detail.loading = false;
                    });
            },
            getDetailPomdes(id){
                let that = this;
                this.resetModal();
                $('#modal-pomdes').modal('show');
                this.detail.loadingPomdes = true;
                this.detail.dataPomdes = '';
                this.$axios().get(`users/${id}`)
                    .then(res => {
                        this.detail.loadingPomdes = false;
                        let data = res?.data?.data;
                        console.log(data);
                        this.detail.dataPomdes = data;
                        setTimeout(function(){
                            $(`#wrap-foto-master-profile-pomdes`).html(`<img src="${data?.profile?.photo_profile?.link+'?token='+that.token}" style="width:150px; height:150px; object-fit: cover; border-radius:100%; border:1px solid black;">`);
                        },300);
                    })
                    .catch(err => {
                        this.$axiosHandleError(err);
                    })
                    .then(()=>{
                        this.detail.loading = false;
                    });
            },
            hapus(id){
                let that = this;
                Swal.fire({
                    title: `Hapus user yang dipilih?`,
                    html: `User akan dihapus dan data pada user tersebut akan terhapus.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Hapus',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#DB3700'
                }).then(result => {
                    if(result.isConfirmed){
                        this.$pageLoadingShow();
                        this.$axios().delete(`users/delete/${id}`)
                            .then(res => {
                                Swal.fire('Berhasil', 'User berhasil dihapus','success');
                                this.getDataTable();
                            })
                            .catch(err =>{
                                this.$axiosHandleError(err);
                            })
                            .then(()=>{
                                this.$pageLoadingHide();
                            });
                    }
                });
            },
            showReject(){
                $('#modal-reject').modal('show');
            }
        }
    }
</script>

<style scope>
    .table-pomdes table thead,.table-pomdes table tbody,.table-pomdes table tr td,.table-pomdes table tr th{
        border:1px solid black !important;
    }
    .table-pomdes table tr td,.table-pomdes table tr th{
        padding:10px !important;
    }
</style>
