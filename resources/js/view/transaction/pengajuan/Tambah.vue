<template>
    <div>
        <div id="main-content">
            <div class="post d-flex flex-column-fluid" id="kt_post">
                <div id="kt_content_container" class="container-xxl">
                    <div class="d-flex justify-content-end">
                        <div class="card-header border-0 pt-5 align-items-center" style="justify-content:flex-end;">
                            <button class="btn btn-secondary" @click="$router.push({name: 't-pengajuan'})"><i class="bi bi-arrow-left fa-lg"></i> Kembali</button>
                        </div>
                    </div>
                    <br>
                    <div class="card mb-5 mb-xl-8">
                        <div class="card-body pt-5 container">
                            <h2>Form Pengajuan Pembelian BBM</h2>
                            <div class="wrap-form">
                                <div class="form my-5">
                                    <label for="name"><h5>Nama Transaksi</h5></label>
                                    <input type="text" class="form-control" placeholder="Masukkan nama transaksi">
                                </div>
                                <div class="form my-5">
                                    <label for="name"><h5>Tanggal Mulai</h5></label>
                                    <input type="text" class="form-control" placeholder="Masukkan tanggal mulai">
                                </div>
                                <div class="form my-5">
                                    <label for="name"><h5>Tanggal Selesai</h5></label>
                                    <input type="text" class="form-control" placeholder="Masukkan tanggal selesai">
                                </div>
                                <div class="form my-5">
                                    <label for="name"><h5>Deskripsi</h5></label>
                                    <textarea class="form-control" rows="5" placeholder="Masukkan deskripsi transaksi"></textarea>
                                </div>
                            </div>
                            <br><br>
                            <div class="card" style="border:1px solid gold;">
                                <div class="card-body">
                                    <h3 style="color:gold;">Bahan Bakar</h3>
                                    <br>
                                    <div class="wrap-bbm">
                                        
                                    </div>
                                </div>
                            </div>
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
                token: localStorage.getItem('pomdes_token'),
                selectList: {
                    province: {
                        loading: false,
                        list: []
                    }
                },
                form: {
                    flag: 'tambah',
                    isEdit: false,
                    idEdit: '',
                    data: {
                        province: '',
                        province_id: '',
                        name: '',
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
                                text: 'Nama Kota',
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
                                text: 'Nama Provinsi',
                                sort_by: 'province_id',
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
                    isEdit: false,
                    idEdit: '',
                    data: {
                        province: '',
                        province_id: '',
                        name: '',
                    }
                }
            },
            simpan(){
                let that = this;
                this.$pageLoadingShow();
                this.form.data.province_id = this.form?.data?.province?.id;
                this.$axios().post(`location/city`, this.form.data)
                    .then(res => {
                        $('.modal').modal('hide');
                        Swal.fire('Berhasil','Data kota berhasil disimpan.','success');
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
                this.$axios().get(`location/city`, {params: this.tableConfig?.config})
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
                $('#modal-form').modal('show');
                this.form.data.name = data?.name;
                this.form.data.province = {id: data?.province?.id, text: data?.province?.name}
            },
            update(){
                let that = this;
                this.$pageLoadingShow();
                this.form.data.province_id = this.form?.data?.province?.id;
                this.$axios().put(`location/city/${this.form.idEdit}`, this.form.data)
                    .then(res => {
                        $('.modal').modal('hide');
                        Swal.fire('Berhasil', 'Data kota berhasil diubah.', 'success');
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
                    title: `Hapus kota yang dipilih?`,
                    html: `Kota akan dihapus dan data pada kota tersebut akan terhapus.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Hapus',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#DB3700'
                }).then(result => {
                    if(result.isConfirmed){
                        this.$pageLoadingShow();
                        this.$axios().delete(`location/city/delete/${id}`)
                            .then(res => {
                                Swal.fire('Berhasil', 'Kota berhasil dihapus','success');
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
            getProvince(search, limit){
                let that = this;
                this.selectList.province.loading = true;

                let data = {
                    search,
                    limit
                };

                this.$axios().get(`location/province/select-list`, {params: data})
                    .then(res => {
                        let data = res?.data?.data;
                        this.selectList.province.list = [];
                        $.each(data, function(i,val){
                            that.selectList.province.list.push({id: val?.id, text: val?.name});
                        });
                    })
                    .catch(err => {
                        this.$axiosHandleError(err);
                    })
                    .then(() => {
                        this.selectList.province.loading = false;
                    })
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
