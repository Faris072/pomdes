<template>
    <div>
        <div id="main-content">
            <div class="post d-flex flex-column-fluid" id="kt_post">
                <div id="kt_content_container" class="container-xxl">
                    <div class="card card-flush mt-5 mb-5 mb-xl-10" id="kt_profile_details_view">
                        <div class="card card-xl-stretch mb-5 mb-xl-8">
                            <div class="card-header border-0 pt-5 align-items-center" style="justify-content:flex-end;">
                                <button class="btn btn-warning" @click="tambah()">Tambah Data</button>
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
                                                <b>{{ context?.name }}</b>
                                            </td>
                                            <td valign="middle">
                                                <b>{{ $moment(context?.created_at).format('DD-MM-YYYY H:m') }}</b>
                                            </td>
                                            <td valign="middle" class="text-center">
                                                <button class="btn btn-secondary dropdown-toggle btn-sm m-auto" type="button" data-bs-toggle="dropdown">Aksi</button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item" href="#" style="padding:10px;" @click="edit(context?.id, context)">Edit</a>
                                                    <a class="dropdown-item" href="#" style="padding:10px;" @click="hapus(context.id)">Hapus</a>
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
                                <h3 class="modal-title">{{ form.flag == 'tambah' ? 'Tambah' : 'Edit' }} Provinsi</h3>
                                <span class="text-muted">Isi form berikut untuk {{ form.flag == 'tambah' ? 'menambah' : 'mengubah' }} provinsi PDOMS</span>
                            </center>
                        </div>
                        <!--begin::Close-->
                        <button type="button" class="btn-close m-2" data-bs-dismiss="modal" aria-label="Close"></button>
                        <!--end::Close-->
                    </div>

                    <div class="modal-body">
                        <label for="name"><h5>Provinsi</h5></label>
                        <input type="text" class="form-control" v-model="form.data.name" placeholder="Isi nama provinsi">
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Tutup</button>
                        <button type="button" class="btn btn-primary" @click="simpan()" v-if="form.flag == 'tambah'">Simpan</button>
                        <button type="button" class="btn btn-primary" @click="update()" v-else>Update</button>
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
                token: localStorage.getItem('pomdes_token'),
                form: {
                    flag: 'tambah',
                    isEdit: false,
                    idEdit: '',
                    data: {
                        name: ''
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
                                text: 'Nama Provinsi',
                                sort_by: 'name',
                                sort: true,
                                class: {
                                    column: '',
                                    wrap: '',
                                    text: ''
                                },
                                style: {
                                    column: '',
                                    wrap: '',
                                    text: '',
                                }
                            },
                            {
                                text: 'Tanggal Pembuatan',
                                sort_by: 'created_at',
                                sort: true,
                                class: {
                                    column: '',
                                    wrap: '',
                                    text: ''
                                },
                                style: {
                                    column: '',
                                    wrap: '',
                                    text: '',
                                }
                            },
                            {
                                text: 'Aksi',
                                sort_by: '',
                                sort: false,
                                class: {
                                    column: '',
                                    wrap: '',
                                    text: 'text-center'
                                },
                                style: {
                                    column: 'width:20%;',
                                    wrap: '',
                                    text: '',
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
                        totalPage: 20,
                        search: ''
                    }
                },
            }
        },
        mounted(){
            this.getDataTable();
        },
        methods: {
            tambah(){
                this.resetModal();
                $('#modal-form').modal('show');
            },
            resetModal(){
                this.form = {
                    flag: 'tambah',
                    loading: false,
                    idEdit: '',
                    data: {
                        name: ''
                    }
                }
            },
            simpan(){
                let that = this;
                this.$pageLoadingShow();
                this.$axios().post(`location/province`, this.form.data)
                    .then(res => {
                        $('.modal').modal('hide');
                        Swal.fire('Berhasil','Data provinsi berhasil disimpan.','success');
                        this.getDataTable();
                    })
                    .catch(err => {
                        this.$axiosHandleError(err);
                    })
                    .then(() => {
                        this.$pageLoadingHide();
                    })
            },
            getDataTable(){
                let that = this;
                this.tableConfig.config.loading = true;
                this.$axios().get(`location/province`, {params: this.tableConfig?.config})
                    .then(res => {
                        let data = res?.data?.data;
                        this.tableConfig.data.body = res?.data?.data?.data;
                        this.tableConfig.config.currentPage = data?.current_page;
                        this.tableConfig.config.limit = data?.per_page;
                        this.tableConfig.config.totalPage = data?.last_page;
                    })
                    .catch(err => {
                        this.$axiosHandleError(err);
                    })
                    .then(() => {
                        this.tableConfig.config.loading = false;
                    })
            },
            edit(id,data){
                this.resetModal();
                this.form.flag = 'edit';
                this.form.idEdit = id;
                this.form.loading = true;
                $('#modal-form').modal('show');
                this.form.data.name = data?.name;
            },
            update(){
                let that = this;
                this.$pageLoadingShow();
                this.$axios().put(`location/province/${this.form.idEdit}`, this.form.data)
                    .then(res => {
                        $('.modal').modal('hide');
                        Swal.fire('Berhasil', 'Data provinsi berhasil diubah.', 'success');
                        this.getDataTable();
                    })
                    .catch(err => {
                        this.$axiosHandleError(err);
                    })
                    .then(() => {
                        this.$pageLoadingHide();
                    });
            },
            hapus(id){
                Swal.fire({
                    title: `Hapus provinsi yang dipilih?`,
                    html: `Provinsi akan dihapus dan data pada provinsi tersebut akan terhapus.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Hapus',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#DB3700'
                }).then(result => {
                    if(result.isConfirmed){
                        this.$pageLoadingShow();
                        this.$axios().delete(`location/province/delete/${id}`)
                            .then(res => {
                                Swal.fire('Berhasil', 'Provinsi berhasil dihapus','success');
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
