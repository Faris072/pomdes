''<template>
    <div>
        <div id="main-content">
            <div class="post d-flex flex-column-fluid" id="kt_post">
                <div id="kt_content_container" class="container-xxl">
                    <div class="card card-flush mt-5 mb-5 mb-xl-10" id="kt_profile_details_view">
                        <div class="card card-xl-stretch mb-5 mb-xl-8">
                            <div class="card-header border-0 pt-5 align-items-center" style="justify-content:flex-end;">
                                <button class="btn btn-warning" @click="$router.push({name: 't-tagihan-tambah'})">Tambah Data</button>
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
                                            <td valign="middle">
                                                <b>{{ context?.description }}</b>
                                            </td>
                                            <td valign="middle">
                                                <span :class="`badge badge-light-primary`" v-if="context?.status_id == 1"><b>{{ context?.status?.name }}</b></span>
                                                <span :class="`badge badge-light-danger`" v-if="context?.status_id == 2"><b>{{ context?.status?.name }}</b></span>
                                                <span :class="`badge badge-light-warning`" v-if="context?.status_id == 3"><b>{{ context?.status?.name }}</b></span>
                                            </td>
                                            <td valign="middle" class="text-center">
                                                <button class="btn btn-secondary dropdown-toggle btn-sm m-auto" type="button" data-bs-toggle="dropdown">Aksi</button>
                                                <div class="dropdown-menu">
                                                    <a class="dropdown-item" href="#" style="padding:10px;" @click="approve(context?.id)"><i class="bi bi-check2-circle fa-lg me-2"></i> Setujui Pengajuan</a>
                                                    <a class="dropdown-item" href="#" style="padding:10px;" @click="showReject(context?.id)"><i class="bi bi-x-circle fa-lg me-2"></i> Tolak Pengajuan</a>
                                                    <a class="dropdown-item" href="#" style="padding:10px;" @click="alasanPenolakan(context?.id, context?.status_id)"><i class="bi bi-info-circle fa-lg me-2"></i> Alasan Penolakan</a>
                                                    <a class="dropdown-item" href="#" style="padding:10px;" @click="perbaikan(context?.id)"><i class="bi bi-wrench-adjustable fa-lg me-2"></i> Ajukan Perbaikan</a>
                                                    <a class="dropdown-item" href="#" style="padding:10px;" @click="showDetail(context?.id)"><i class="bi bi-info-lg fa-lg me-2"></i> Detail</a>
                                                    <a class="dropdown-item" href="#" style="padding:10px;" @click="$router.push({path: `pengajuan/edit/${context.id}`})"><i class="bi bi-pencil-square fa-lg me-2"></i> Edit</a>
                                                    <a class="dropdown-item" href="#" style="padding:10px;" @click="hapus(context.id)"><i class="bi bi-trash fa-lg me-2"></i> Hapus</a>
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

        <div class="modal fade" tabindex="-1" id="modal-detail">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="m-auto" style="width:100%;">
                            <center>
                                <h3 class="modal-title">Detail Pengajuan</h3>
                                <span class="text-muted">Berikut adalah detail pengajuan transaksi pada aplikasi PDOMS</span>
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
                            <div class="info-bbm container">
                                <div class="row my-3">
                                    <div class="col-md-4">
                                        <h5 class="text-muted">User</h5>
                                    </div>
                                    <div class="col-md-8">
                                        <h5>{{ detail?.data?.user || '-' }}</h5>
                                    </div>
                                </div>
                                <div class="row my-3">
                                    <div class="col-md-4">
                                        <h5 class="text-muted">Nama Transaksi</h5>
                                    </div>
                                    <div class="col-md-8">
                                        <h5>{{ detail?.data?.nama || '-' }}</h5>
                                    </div>
                                </div>
                                <div class="row my-3">
                                    <div class="col-md-4">
                                        <h5 class="text-muted">Deskripsi Transaksi</h5>
                                    </div>
                                    <div class="col-md-8">
                                        <h5>{{ detail?.data?.deskripsi || '-' }}</h5>
                                    </div>
                                </div>
                                <div class="row my-3">
                                    <div class="col-md-4">
                                        <h5 class="text-muted">File Pendukung</h5>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="row mb-5">
                                            <div class="col-md-6 my-3" v-for="(context, index) in detail.data.files">
                                                <div class="card card-file">
                                                    <a :href="`${context?.link}?token=${token}`" :download="context?.name">
                                                        <div class="card-body p-2 d-flex align-items-center">
                                                            <div class="icon-file">
                                                                <img src="@/assets/images/file_icon.png" style="width:50px;">
                                                            </div>
                                                            <div class="info-file">
                                                                <h6 class="text-primary">{{ context?.name || '-' }}</h6>
                                                                <span class="text-muted">{{ $formatBytes(context?.size) }}</span>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <br>
                                <!--begin::Accordion-->
                                <div class="accordion" id="accordion-pengajuan">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="kt_accordion_1_header_1">
                                            <button class="accordion-button fs-4 fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#accordion-pengajuan-content" aria-expanded="true" aria-controls="accordion-pengajuan">Bahan Bakar yang Dipesan</button>
                                        </h2>
                                        <div id="accordion-pengajuan-content" class="accordion-collapse collapse show" aria-labelledby="kt_accordion_1_header_1" data-bs-parent="#accordion-pengajuan">
                                            <div class="accordion-body">
                                                <h5>Supplier: {{ detail?.data?.supplier || '-' }}</h5>
                                                <div class="table-pomdes" style="overflow:auto;">
                                                    <table class="table table-bordered" style="border:1px solid black;">
                                                        <thead style="background-color:#F5F8FA !important;">
                                                            <tr>
                                                                <th class="text-center"><b>No</b></th>
                                                                <th><b>Jenis BBM</b></th>
                                                                <th><b>Volume BBM (Liter)</b></th>
                                                                <th><b>Harga BBM (Rp)</b></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr v-for="(context, index) in detail?.data?.fuel">
                                                                <td class="text-center">{{ index+1 }}</td>
                                                                <td>{{ context?.fuel?.name }}</td>
                                                                <td>{{ $rupiahFormat(context?.volume) }}</td>
                                                                <td>{{ $rupiahFormat(context?.price) }}</td>
                                                            </tr>
                                                        </tbody>
                                                        <tr>
                                                            <td colspan="2"><b>Total</b></td>
                                                            <td><b>{{ countVolumeBbm }} Liter</b></td>
                                                            <td><b>Rp{{ countPriceBbm }}</b></td>
                                                        </tr>
                                                    </table>
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

        <div class="modal fade" tabindex="-1" id="modal-reject">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="m-auto" style="width:100%;">
                            <center>
                                <h3 class="modal-title">Tolak Pengajuan</h3>
                                <span class="text-muted">Isi form berikut ini untuk mengisi alasan penolakan</span>
                            </center>
                        </div>
                        <!--begin::Close-->
                        <button type="button" class="btn-close m-2" data-bs-dismiss="modal" aria-label="Close"></button>
                        <!--end::Close-->
                    </div>

                    <div class="modal-body" style="overflow-x:auto;">
                        <div class="form">
                            <label for="alasan-penolakan"><h5>Alasan Penolakan</h5></label>
                            <textarea id="alasan-penolakan" rows="5" class="form-control" placeholder="Cth: Stok supplier telah habis" v-model="reject.data.description"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" @click="tolak()">Tolak Pengajuan</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" tabindex="-1" id="modal-alasan">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="m-auto" style="width:100%;">
                            <center>
                                <h3 class="modal-title">Alasan penolakan</h3>
                                <span class="text-muted">Berikut adalah alasan penolakan dari transaksi yang dipilih.</span>
                            </center>
                        </div>
                        <!--begin::Close-->
                        <button type="button" class="btn-close m-2" data-bs-dismiss="modal" aria-label="Close"></button>
                        <!--end::Close-->
                    </div>

                    <div class="modal-body" style="overflow-x:auto;">
                        <div class="loading d-flex justify-content-center align-items-center" v-if="reasonReject.loading">
                            <app-loader></app-loader>
                        </div>
                        <div class="form" v-else>
                            <label for="alasan-penolakan"><h5>Alasan Penolakan</h5></label>
                            <div class="card" style="border:1px solid gold;">
                                <div class="card-body p-5">
                                    {{ reasonReject.description }}
                                </div>
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
                detail: {
                    loading: false,
                    data: {
                        user:'',
                        nama:'',
                        deskripsi:'',
                        supplier: '',
                        fuel:[],
                        files:[]
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
                                text: 'Nama Transaksi',
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
                                text: 'Keterangan',
                                sort_by: 'description',
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
                                text: 'Status',
                                sort_by: 'status_id',
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
                        order_by: 'desc',
                        sort_by: 'id',
                        loading: false,
                        limit: 10,
                        currentPage: 1,
                        totalPage: 20,
                        search: ''
                    }
                },
                reject: {
                    idReject: '',
                    data: {
                        description: '',
                    }
                },
                reasonReject: {
                    loading: false,
                    description: ''
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
            resetDetail(){
                this.detail = {
                    loading: false,
                    data: {
                        user:'',
                        nama:'',
                        deskripsi:'',
                        supplier: '',
                        fuel:[],
                        files:[]
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
                this.$axios().get(`transaction/data-table/2`, {params: this.tableConfig?.config})
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
            },
            showDetail(id){
                let that = this;

                this.resetDetail();
                $('#modal-detail').modal('show');
                this.detail.loading = true;
                this.$axios().get(`transaction/${id}`)
                    .then(res => {
                        let data = res?.data?.data;
                        console.log(data);
                        this.detail.data = {
                            user: data?.user?.username,
                            nama: data?.name,
                            deskripsi:data?.description,
                            supplier: data?.fuel_transactions?.length ? data?.fuel_transactions[0]?.fuel?.supplier?.username : '-',
                            fuel: data?.fuel_transactions,
                            files: data?.submission_files
                        };
                    })
                    .catch(err => {
                        this.$axiosHandleError(err);
                    })
                    .then(() => {
                        this.detail.loading = false;
                    });
            },
            showReject(id){
                this.reject={
                    idReject: id,
                    data: {
                        description: '',
                    }
                };
                $('#modal-reject').modal('show');
            },
            tolak(){
                this.$pageLoadingShow();
                this.$axios().post(`transaction/reject/${this.reject.idReject}`, this.reject.data)
                    .then(res => {
                        $('.modal').modal('hide');
                        Swal.fire('Berhasil', 'Pengajuan berhasil ditolak','success');
                        this.getDataTable();
                    })
                    .catch(err =>{
                        this.$axiosHandleError(err);
                    })
                    .then(()=>{
                        this.$pageLoadingHide();
                    });
            },
            perbaikan(id){
                Swal.fire({
                    title: `Perbaiki pengajuan yang dipilih?`,
                    html: `Pastikan data sudah lengkap dan sesuai sebelum mengajukan perbaikan.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Ajukan Perbaikan',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#41FF1C'
                }).then(result => {
                    if(result.isConfirmed){
                    this.$pageLoadingShow();
                    this.$axios().post(`transaction/repair/${id}`)
                        .then(res => {
                            $('.modal').modal('hide');
                            Swal.fire('Berhasil', 'Perbaikan berhasil diajukan','success');
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
            alasanPenolakan(id, status_id){
                this.reasonReject = {
                    loading: true,
                    description: ''
                };

                $('#modal-alasan').modal('show');

                this.$axios().post(`transaction/reason-reject/${id}`, {status_id: status_id})
                    .then(res => {
                        this.reasonReject.description = res?.data?.data?.description;
                    })
                    .catch(err =>{
                        this.$axiosHandleError(err);
                    })
                    .then(()=>{
                        this.reasonReject.loading = false;
                    });
            },
            approve(id){
                Swal.fire({
                    title: `Setujui pengajuan yang dipilih?`,
                    html: `Pengajuan akan diteruskan ke tahap selanjutnya.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Setujui',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#41FF1C'
                }).then(result => {
                    if(result.isConfirmed){
                    this.$pageLoadingShow();
                    this.$axios().put(`transaction/approve-submission/${id}`)
                        .then(res => {
                            Swal.fire('Berhasil', 'Pengajuan berhasil disetujui','success');
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
        },
        computed: {
            countPriceBbm(){
                let that = this;
                let price = 0;
                $.each(this?.detail?.data?.fuel, function(i,val){
                    price += Number(val?.price);
                });
                return this.$rupiahFormat(price);
            },
            countVolumeBbm(){
                let that = this;
                let volume = 0;
                $.each(this?.detail?.data?.fuel, function(i,val){
                    volume += Number(val?.volume);
                });
                return this.$rupiahFormat(volume);
            },
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
