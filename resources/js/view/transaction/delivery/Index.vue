''<template>
    <div>
        <div id="main-content">
            <div class="post d-flex flex-column-fluid" id="kt_post">
                <div id="kt_content_container" class="container-xxl">
                    <div class="card card-flush mt-5 mb-5 mb-xl-10" id="kt_profile_details_view">
                        <div class="card card-xl-stretch mb-5 mb-xl-8">
                            <div class="card-header border-0 pt-5 align-items-center" style="justify-content:flex-end;">
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
                                                <b>{{ context?.user?.username || '-'}} {{ context?.user?.profile?.name ? `( ${context?.user?.profile?.name} )` : ''}}</b>
                                            </td>
                                            <td valign="middle">
                                                <b>{{ context?.fuel_transactions ? context?.fuel_transactions[0]?.fuel?.supplier?.username : '-'}}</b>
                                            </td>
                                            <td valign="middle">
                                                <span :class="`badge badge-light-warning`" v-if="context?.status_id == 7"><b>{{ context?.status?.name }}</b></span>
                                                <span :class="`badge badge-light-success`" v-if="context?.status_id == 8"><b>{{ context?.status?.name }}</b></span>
                                                <span :class="`badge badge-light-danger`" v-if="context?.status_id == 9"><b>{{ context?.status?.name }}</b></span>
                                            </td>
                                            <td valign="middle" class="text-center">
                                                <button class="btn btn-secondary dropdown-toggle btn-sm m-auto" type="button" data-bs-toggle="dropdown">Aksi</button>
                                                <div class="dropdown-menu">
                                                    <template v-if="context?.status_id == 7">
                                                        <a class="dropdown-item" href="#" style="padding:10px;" @click="showDetail(context?.id)"><i class="bi bi-info-lg fa-lg me-2"></i> Detail</a>
                                                        <a class="dropdown-item" href="#" style="padding:10px;" @click="sendDelivery(context?.id)"><i class="bi bi-truck fa-lg"></i> Kirim BBM</a>
                                                        <a class="dropdown-item" href="#" style="padding:10px;" @click="modalBukti(context?.id)"><i class="bi bi-camera fa-lg"></i> {{ context?.delivery ? 'Edit' : '' }} Bukti Pengiriman</a>
                                                    </template>
                                                    <template v-if="context?.status_id == 8">
                                                        <a class="dropdown-item" href="#" style="padding:10px;" @click="showDetail(context?.id)"><i class="bi bi-info-lg fa-lg me-2"></i> Detail</a>
                                                        <a class="dropdown-item" href="#" style="padding:10px;" @click="sendHindrance(context?.id)"><i class="bi bi-send-exclamation fa-lg"></i> Kirim Laporan Kendala</a>
                                                        <a class="dropdown-item" href="#" style="padding:10px;" @click="modalKendala(context?.id)"><i class="bi bi-exclamation-triangle fa-lg"></i> {{ context?.delivery ? 'Edit' : '' }} Laporan Kendala</a>
                                                    </template>
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
                                        <h5 class="text-muted">Estimasi Sampai</h5>
                                    </div>
                                    <div class="col-md-8">
                                        <h5>{{ $moment(detail?.data?.delivery?.estimation_date).format('DD-MM-YYYY') || '-' }}</h5>
                                    </div>
                                </div>
                                <div class="row my-3">
                                    <div class="col-md-4">
                                        <h5 class="text-muted">Deskripsi Pengiriman</h5>
                                    </div>
                                    <div class="col-md-8">
                                        <h5>{{ detail?.data?.delivery?.description || '-' }}</h5>
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
                                <div class="row my-3" v-if="detail.data.invoice?.invoice_pomdes_files?.length">
                                    <div class="col-md-4">
                                        <h5 class="text-muted">Lampiran File Tagihan</h5>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="row mb-5">
                                            <div class="col-md-6 my-3" v-for="(context, index) in detail.data.invoice?.invoice_pomdes_files">
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
                                <div class="row my-3" v-if="detail.data.delivery?.delivery_files?.length">
                                    <div class="col-md-4">
                                        <h5 class="text-muted">Lampiran File Bukti Pengiriman</h5>
                                    </div>
                                    <div class="col-md-8">
                                        <div class="row mb-5">
                                            <div class="col-md-6 my-3" v-for="(context, index) in detail.data.delivery?.delivery_files">
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
                                            <button class="accordion-button fs-4 fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#accordion-pengajuan-content" aria-expanded="true" aria-controls="accordion-pengajuan">Detail Nominal Transaksi</button>
                                        </h2>
                                        <div id="accordion-pengajuan-content" class="accordion-collapse collapse show" aria-labelledby="kt_accordion_1_header_1" data-bs-parent="#accordion-pengajuan">
                                            <div class="accordion-body">
                                                <h5>Supplier: {{ detail?.data?.supplier || '-' }}</h5>
                                                <div class="table-pomdes" style="overflow:auto;">
                                                    <center><h5>Pesanan BBM</h5></center>
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
                                                            <td><b>{{ $rupiahFormat(countVolumeBbm) }} Liter</b></td>
                                                            <td><b>Rp{{ $rupiahFormat(countPriceBbm) }}</b></td>
                                                        </tr>
                                                    </table>
                                                    <br>
                                                    <center><h5>Biaya Tambahan</h5></center>
                                                    <table class="table table-bordered" style="border:1px solid black;">
                                                        <thead style="background-color:#F5F8FA !important;">
                                                            <tr>
                                                                <th class="text-center"><b>No</b></th>
                                                                <th><b>Nama Biaya Tambahan</b></th>
                                                                <th><b>Nominal</b></th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr v-for="(context, index) in detail?.data?.invoice?.additional_costs" v-if="detail?.data?.invoice?.additional_costs?.length">
                                                                <td class="text-center">{{ index+1 }}</td>
                                                                <td>{{ context?.name }}</td>
                                                                <td>{{ $rupiahFormat(context?.nominal) }}</td>
                                                            </tr>
                                                            <tr v-else>
                                                                <td colspan="3">Tidak ada biaya tambahan</td>
                                                            </tr>
                                                        </tbody>
                                                        <tr>
                                                            <td colspan="2"><b>Total</b></td>
                                                            <td><b>Rp{{ $rupiahFormat(countAdditionalCosts) }}</b></td>
                                                        </tr>
                                                    </table>
                                                </div>
                                                <br>
                                                <h5><b>Total Biaya: Rp{{ $rupiahFormat(countPriceBbm + countAdditionalCosts) }}</b></h5>
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

        <div class="modal fade" tabindex="-1" id="modal-bukti">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="m-auto" style="width:100%;">
                            <center>
                                <h3 class="modal-title">Form Bukti Pengiriman</h3>
                                <span class="text-muted">Isi form bukti pengiriman BBM</span>
                            </center>
                        </div>
                        <!--begin::Close-->
                        <button type="button" class="btn-close m-2" data-bs-dismiss="modal" aria-label="Close"></button>
                        <!--end::Close-->
                    </div>

                    <div class="modal-body">
                        <div class="loading d-flex justify-content-center align-items-center" style="height:100%;" v-if="form.loading">
                            <app-loader></app-loader>
                        </div>
                        <div class="wrap-form" v-else>
                            <div class="form">
                                <label for="estimasi"><h5>Estimasi Sampai : </h5></label>
                                <br>
                                <app-datepicker v-model:value="form.estimationDate" format="DD-MM-YYYY" value-type="YYYY-MM-DD" placeholder="Pilih tanggal"></app-datepicker>
                                <br><br>
                                <label for="deskripsi"><h5>Deskripsi : </h5></label>
                                <textarea v-model="form.description" id="deskripsi" class="form-control" rows="3" placeholder="Isikan deskripsi"></textarea>
                            </div>
                            <br>
                            <div class="show-files" v-if="form.showFiles?.length">
                                <h5>File Terlampir : </h5>
                                <div class="row my-3">
                                    <div class="col-md-6 my-3" v-for="(context, index) in form.showFiles">
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
                            <div class="file my-5">
                                <div class="dropzone" id="dropzoe-file" style="border:2px dashed gold; background-color:#fffdf1;">
                                    <div class="dz-message needsclick">
                                        <i class="bi bi-file-earmark-arrow-up text-warning fs-3x"></i>
                                        <div class="ms-4">
                                            <h3 class="fs-5 fw-bolder text-gray-900 mb-1">Drop files here or click to upload.</h3>
                                            <span class="fs-7 fw-bold text-gray-400">Upload up to 10 files</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-warning" @click="simpan()">Simpan</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" tabindex="-1" id="modal-kendala">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <div class="m-auto" style="width:100%;">
                            <center>
                                <h3 class="modal-title">Form Kendala Pengiriman</h3>
                                <span class="text-muted">Isi form berikut jika ada kendala saat pengiriman.</span>
                            </center>
                        </div>
                        <!--begin::Close-->
                        <button type="button" class="btn-close m-2" data-bs-dismiss="modal" aria-label="Close"></button>
                        <!--end::Close-->
                    </div>

                    <div class="modal-body">
                        <div class="loading d-flex justify-content-center align-items-center" style="height:100%;" v-if="hindrance.loading">
                            <app-loader></app-loader>
                        </div>
                        <div class="wrap-form" v-else>
                            <div class="form">
                                <label for="estimasi"><h5>Deskripsi : </h5></label>
                                <br>
                                <textarea v-model="hindrance.description" rows="3" class="form-control" placeholder="Masukkan deskripsi"></textarea>
                            </div>
                            <br><br>
                            <div class="show-files" v-if="hindrance.showFiles?.length">
                                <h5>File Terlampir : </h5>
                                <div class="row my-3">
                                    <div class="col-md-6 my-3" v-for="(context, index) in hindrance.showFiles">
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
                            <div class="file my-5">
                                <div class="dropzone" id="dropzoe-kendala" style="border:2px dashed gold; background-color:#fffdf1;">
                                    <div class="dz-message needsclick">
                                        <i class="bi bi-file-earmark-arrow-up text-warning fs-3x"></i>
                                        <div class="ms-4">
                                            <h3 class="fs-5 fw-bolder text-gray-900 mb-1">Drop files here or click to upload.</h3>
                                            <span class="fs-7 fw-bold text-gray-400">Upload up to 10 files</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-warning" @click="simpanKendala()">Simpan</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
import Edit from '../pengajuan/Edit.vue';

    export default {
        data(){
            return {
                token: localStorage.getItem('pomdes_token'),
                selectList: {},
                form: {
                    loading: false,
                    id: '',
                    estimationDate: '',
                    description: '',
                    showFiles: [],
                    files: ''
                },
                detail: {
                    loading: false,
                    data: {
                        user:'',
                        nama:'',
                        deskripsi:'',
                        supplier: '',
                        fuel:[],
                        files:[],
                        invoice:[],
                        delivery: []
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
                                text: 'Mitra',
                                sort_by: 'username',
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
                                text: 'Supplier',
                                sort_by: '',
                                sort: false,
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
                hindrance: {
                    loading: false,
                    id: '',
                    description: '',
                    showFiles: [],
                    files: ''
                },
            }
        },
        mounted(){
            this.getDataTable();
        },
        methods: {
            initDropzone(){
                if(this.form.files){
                    this.form.files.destroy();
                }
                this.form.files = new Dropzone("#dropzoe-file", {
                    url: "/",
                    dictCancelUpload: "Cancel",
                    maxFilesize: 50,
                    parallelUploads: 100,
                    uploadMultiple: true,
                    maxFiles: 100,
                    addRemoveLinks: true,
                    acceptedFiles: ".jpg,.jpeg,.png,.pdf,.xlsx,.doc,.docx",
                    autoProcessQueue: false,
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        Authorization: "Bearer " + localStorage.getItem("pomdes_token"),
                    },
                    init: function () {
                        this.on("error", function (file) {
                        if (!file.accepted) {
                            this.removeFile(file);
                            Swal.fire("Silahkan periksa file Anda lagi");
                        } else if (file.status == "error") {
                            this.removeFile(file);
                            Swal.fire("Terjadi kesalahan koneksi");
                        }
                        });
                            this.on("resetFiles", function (file) {
                            this.removeAllFiles();
                        });
                    },
                });
            },
            modalBukti(id){
                this.form.loading = true;
                this.resetModal();
                this.form.id = id;
                this.edit();
                $('#modal-bukti').modal('show');
            },
            resetModal(){
                this.form = {
                    loading:false,
                    id: '',
                    estimationDate: null,
                    description: '',
                    showFiles: [],
                    files: this.form.files
                }
            },
            simpan(){
                let that = this;
                if(this?.form?.files?.files?.length > 0){
                    this.form.files.on("sending",function(file,xhr,data){
                        data.append("estimation_date",that.form.estimationDate);
                        data.append("description",that.form.description);
                    });
                    this.form.files.on('processing', function(){
                        this.options.url = urlApi+`transaction/delivery/${that.form.id}`;
                    });
                    this.$pageLoadingShow();
                    this.form.files.processQueue();
                    this.form.files.on('success', function(){
                        that.$pageLoadingHide();
                        $('.modal').modal('hide');
                        that.getDataTable();
                        Swal.fire('Berhasil', 'Data pengiriman telah disimpan.','success').then(()=>{
                        });
                    });
                }
                else{
                    let data = {
                        estimation_date: this.form.estimationDate,
                        description: this.form.description
                    };
                    this.$pageLoadingShow();
                    this.$axios().post(`transaction/delivery/${this.form.id}`, data)
                        .then(res => {
                            $('.modal').modal('hide');
                            that.getDataTable();
                            Swal.fire('Berhasil', 'Data pengiriman telah disimpan.','success').then(()=>{
                            });
                        })
                        .catch(err => {
                            this.$axiosHandleError(err);
                        })
                        .then(() => {
                            this.$pageLoadingHide();
                        });
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
                        files:[],
                        invoice: [],
                        delivery: []
                    }
                }
            },
            getDataTable(){
                let that = this;
                this.tableConfig.config.loading = true;
                this.$axios().get(`transaction/data-table/4`, {params: this.tableConfig?.config})
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
            showDetail(id){
                let that = this;

                this.resetDetail();
                $('#modal-detail').modal('show');
                this.detail.loading = true;
                this.$axios().get(`transaction/${id}`)
                    .then(res => {
                        let data = res?.data?.data;
                        this.detail.data = {
                            user: data?.user?.username,
                            nama: data?.name,
                            deskripsi:data?.description,
                            supplier: data?.fuel_transactions?.length ? data?.fuel_transactions[0]?.fuel?.supplier?.username : '-',
                            fuel: data?.fuel_transactions,
                            files: data?.submission_files,
                            invoice: data?.invoice_pomdes,
                            delivery: data?.delivery,
                        };
                        console.log(this.detail.data);
                    })
                    .catch(err => {
                        this.$axiosHandleError(err);
                    })
                    .then(() => {
                        this.detail.loading = false;
                    });
            },
            sendDelivery(id){
                Swal.fire({
                    title: `Kirim transaksi yang dipilih?`,
                    html: `Status transaksi akan diubah ke <b>dalam pengiriman</b> dan tidak dapat diubah kembali.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Kirim',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#41FF1C'
                }).then(result => {
                    if(result.isConfirmed){
                    this.$pageLoadingShow();
                    this.$axios().put(`transaction/delivery/send-delivery/${id}`)
                        .then(res => {
                            Swal.fire('Berhasil', 'Status transaksi berhasil diubah ke dalam pengiriman','success');
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
            edit(){
                let that = this;
                this.form.loading = true;
                let id = this.form.id;
                this.$axios().get(`transaction/${id}`)
                    .then(res => {
                        let data = res?.data?.data?.delivery;
                        if(!data){
                            return true;
                        }

                        this.form.estimationDate = data?.estimation_date;
                        this.form.description = data?.description;
                        this.form.showFiles = data?.delivery_files;
                    })
                    .catch(err => {
                        this.$axiosHandleError(err);
                    })
                    .then(() => {
                        this.form.loading = false;
                        setTimeout(function(){
                            that.initDropzone();
                        },200);
                    });
            },
            initDropzoneKendala(){
                if(this.hindrance.files){
                    this.hindrance.files.destroy();
                }
                this.hindrance.files = new Dropzone("#dropzoe-kendala", {
                    url: "/",
                    dictCancelUpload: "Cancel",
                    maxFilesize: 50,
                    parallelUploads: 100,
                    uploadMultiple: true,
                    maxFiles: 100,
                    addRemoveLinks: true,
                    acceptedFiles: ".jpg,.jpeg,.png,.pdf,.xlsx,.doc,.docx",
                    autoProcessQueue: false,
                    headers: {
                        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                        Authorization: "Bearer " + localStorage.getItem("pomdes_token"),
                    },
                    init: function () {
                        this.on("error", function (file) {
                        if (!file.accepted) {
                            this.removeFile(file);
                            Swal.fire("Silahkan periksa file Anda lagi");
                        } else if (file.status == "error") {
                            this.removeFile(file);
                            Swal.fire("Terjadi kesalahan koneksi");
                        }
                        });
                            this.on("resetFiles", function (file) {
                            this.removeAllFiles();
                        });
                    },
                });
            },
            resetModalKendala(){
                this.hindrance = {
                    loading:false,
                    id: '',
                    description: null,
                    showFiles: [],
                    files: this.hindrance.files
                }
            },
            modalKendala(id){
                this.hindrance.loading = true;
                this.resetModalKendala();
                this.hindrance.id = id;
                this.editKendala();
                $('#modal-kendala').modal('show');
            },
            editKendala(){
                let that = this;
                this.hindrance.loading = true;
                let id = this.hindrance.id;
                this.$axios().get(`transaction/${id}`)
                    .then(res => {
                        let data = res?.data?.data?.hindrance;
                        if(!data){
                            return true;
                        }

                        this.hindrance.description = data?.description;
                        this.hindrance.showFiles = data?.hindrance_files;
                    })
                    .catch(err => {
                        this.$axiosHandleError(err);
                    })
                    .then(() => {
                        this.hindrance.loading = false;
                        setTimeout(function(){
                            that.initDropzoneKendala();
                        },200);
                    });
            },
            simpanKendala(){
                let that = this;
                if(this?.hindrance?.files?.files?.length > 0){
                    this.hindrance.files.on("sending",function(file,xhr,data){
                        data.append("description",that.hindrance.description);
                    });
                    this.hindrance.files.on('processing', function(){
                        this.options.url = urlApi+`transaction/hindrance/${that.hindrance.id}`;
                    });
                    this.$pageLoadingShow();
                    this.hindrance.files.processQueue();
                    this.hindrance.files.on('success', function(){
                        that.$pageLoadingHide();
                        $('.modal').modal('hide');
                        Swal.fire('Berhasil', 'Laporan kendala berhasil disimpan.','success').then(()=>{
                            that.getDataTableKendala();
                        });
                    });
                }
                else{
                    let data = {
                        description: this.hindrance.description
                    };
                    this.$pageLoadingShow();
                    this.$axios().post(`transaction/hindrance/${this.form.id}`, data)
                        .then(res => {
                            $('.modal').modal('hide');
                            Swal.fire('Berhasil', 'Data kendala berhasil dikirimkan.','success').then(()=>{
                                that.getDataTable();
                            });
                        })
                        .catch(err => {
                            this.$axiosHandleError(err);
                        })
                        .then(() => {
                            this.$pageLoadingHide();
                        });
                }
            },
            sendHindrance(id){
                Swal.fire({
                    title: `Kirim kendala untuk transaksi yang dipilih?`,
                    html: `Status transaksi akan diubah ke <b>kendala pengiriman</b> dan tidak dapat diubah kembali.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Kirim',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#41FF1C'
                }).then(result => {
                    if(result.isConfirmed){
                    this.$pageLoadingShow();
                    this.$axios().put(`transaction/hindrance/send-hindrance/${id}`)
                        .then(res => {
                            Swal.fire('Berhasil', 'Status transaksi berhasil diubah ke kendala','success');
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
        },
        computed: {
            countPriceBbm(){
                let that = this;
                let price = 0;
                $.each(this?.detail?.data?.fuel, function(i,val){
                    price += Number(val?.price);
                });
                return price;
            },
            countVolumeBbm(){
                let that = this;
                let volume = 0;
                $.each(this?.detail?.data?.fuel, function(i,val){
                    volume += Number(val?.volume);
                });
                return volume;
            },
            countAdditionalCosts(){
                let that = this;
                let nominal = 0;
                $.each(this?.detail?.data?.invoice?.additional_costs, function(i,val){
                    nominal += Number(val?.nominal);
                });
                return nominal;
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

