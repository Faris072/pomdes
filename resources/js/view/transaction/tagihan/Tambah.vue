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
                        <div class="card-body container">
                            <div class="informasi my-5">
                                <h1>Form Penerbitan Tagihan</h1>
                                <br>
                                <div class="informasi">
                                    <div class="card" style="box-shadow:0px 0px 10px lightgray;">
                                        <div class="card-body">
                                            <h2 class="text-warning">Informasi Pesanan</h2>
                                            <br>
                                            <div class="row my-4">
                                                <div class="col-md-3">
                                                    <h5 class="text-gray-700">Nama Pemesan</h5>
                                                </div>
                                                <div class="col-md-9">
                                                    <h5 class="text-primary" style="cursor:pointer;" @click="detailProfile()">Nama Pemesan</h5>
                                                </div>
                                            </div>
                                            <div class="row my-4">
                                                <div class="col-md-3">
                                                    <h5 class="text-gray-700">Nama Transaksi</h5>
                                                </div>
                                                <div class="col-md-9">
                                                    <h5>Transaksi A</h5>
                                                </div>
                                            </div>
                                            <div class="row my-4">
                                                <div class="col-md-3">
                                                    <h5 class="text-gray-700">Tanggal Pengajuan</h5>
                                                </div>
                                                <div class="col-md-9">
                                                    <h5>1 Januari 2022 09:00</h5>
                                                </div>
                                            </div>
                                            <div class="row my-4">
                                                <div class="col-md-3">
                                                    <h5 class="text-gray-700">Deskripsi</h5>
                                                </div>
                                                <div class="col-md-9">
                                                    <h5>Lorem ipsum dolor sit amet consectetur, adipisicing elit. Exercitationem, expedita soluta ab in dicta placeat vitae accusamus? Similique, voluptatibus ipsa accusantium at ut corrupti minus aut dicta temporibus, eveniet animi.</h5>
                                                </div>
                                            </div>
                                            <div class="row my-4">
                                                <div class="col-md-12">
                                                    <!--begin::Accordion-->
                                                    <div class="accordion" id="accordion-pengajuan">
                                                        <div class="accordion-item">
                                                            <h2 class="accordion-header" id="kt_accordion_1_header_1">
                                                                <button class="accordion-button fs-4 fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#accordion-pengajuan-content" aria-expanded="true" aria-controls="accordion-pengajuan">Bahan Bakar yang Dipesan</button>
                                                            </h2>
                                                            <div id="accordion-pengajuan-content" class="accordion-collapse collapse show" aria-labelledby="kt_accordion_1_header_1" data-bs-parent="#accordion-pengajuan">
                                                                <div class="accordion-body">
                                                                    <h5>Supplier: Pertamina</h5>
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
                                                                                <tr v-for="(context, index) in 5">
                                                                                    <td class="text-center">{{ index+1 }}</td>
                                                                                    <td>Pertamax</td>
                                                                                    <td>100</td>
                                                                                    <td>10.000.000</td>
                                                                                </tr>
                                                                            </tbody>
                                                                            <tr>
                                                                                <td colspan="2"><b>Total</b></td>
                                                                                <td><b>1.000 Liter</b></td>
                                                                                <td><b>Rp1.000</b></td>
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
                                    </div>
                                </div>
                                <br>
                                <div class="form my-5">
                                    <div class="card" style="box-shadow:0px 0px 10px lightgray;">
                                        <div class="card-body">
                                            <h2 class="text-warning">Form Tagihan</h2>
                                            <br>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <h5 class="text-gray-700">Total Biaya BBM</h5>
                                                </div>
                                                <div class="col-md-9">
                                                    <h5>Rp1.200.000</h5>
                                                </div>
                                            </div>
                                            <br>
                                            <button class="btn btn-warning" @click="tambahBiayaTambahan()">Tambah biaya tambahan</button>
                                            <br><br>
                                            <div class="card" style="border:1px solid gold;" v-if="form.additionalCosts.length">
                                                <div class="card-body">
                                                    <h4>Biaya Tambahan</h4>
                                                    <br>
                                                    <div class="row my-5" v-for="(context, index) in form.additionalCosts">
                                                        <div class="col-md-1 d-flex align-items-center justify-content-between"><h5 class="p-0 m-0">{{ index+1 }}.</h5></div>
                                                        <div class="col-md-5">
                                                            <input type="text" v-model="context.name" class="form-control" placeholder="Masukkan nama biaya tambahan">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <app-money3 class="form-control" v-model="context.nominal" placeholder="Masukkan nominal biaya tambahan" v-bind="money3"></app-money3>
                                                        </div>
                                                        <div class="col-md-2">
                                                            <button class="btn btn-light-danger" @click="hapusBiayaTambahan(index)">Hapus</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <br>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <h5 class="text-gray-700">Total Biaya Transaksi</h5>
                                                </div>
                                                <div class="col-md-9">
                                                    <h5 class="text-gray-700">Rp{{ this.$rupiahFormat(countTotal) }}</h5>
                                                </div>
                                            </div>
                                        </div>
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
                dropzoneFile: '',
                selectList: {
                    fuel: {
                        loading: false,
                        list: []
                    },
                    supplier: {
                        loading: false,
                        list: []
                    },
                    user: {
                        loading: false,
                        list: []
                    },
                },
                form: {
                    total: '',
                    additionalCosts: []
                },
                money3: {
                    masked: false,
                    prefix: '',
                    suffix: '',
                    thousands: '.',
                    decimal: ',',
                    precision: 2,
                    disableNegative: false,
                    disabled: false,
                    min: null,
                    max: null,
                    allowBlank: true,
                    minimumNumberOfCharacters: 0,
                },
            }
        },
        mounted(){
            // this.initDropzone();
            this.resetForm();
        },
        unmounted(){
            // this.dropzoneFile.destroy();
        },
        methods: {
            initDropzone(){
                this.dropzoneFile = new Dropzone("#dropzoe-file", {
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
            resetForm(){
                this.form = {
                    total: '',
                    additionalCosts: []
                }
            },
            tambahBiayaTambahan(){
                this.form.additionalCosts.push({
                    name: '',
                    nominal: ''
                });
            },
            hapusBiayaTambahan(index){
                this.form.additionalCosts.splice(index,1);
            },
            simpan(){
                let that = this;

                let data = this.form;

                data.user_id = data?.user?.id;

                $.each(data?.fuels, function(i,val){
                    val.fuel_id = val?.fuel?.id;
                });

                this.$pageLoadingShow();
                this.$axios().post(`transaction`, data)
                    .then(res => {
                        if(this.dropzoneFile?.files?.length > 0){
                            this.uploadFile(res?.data?.data?.id);
                        }
                        else{
                            this.$pageLoadingHide();
                            Swal.fire('Berhasil', 'Pengajuan transaksi berhasil disimpan.','success').then(()=>{
                                that.$router.push({name: 't-pengajuan'});
                            })
                        }
                    })
                    .catch(err => {
                        this.$pageLoadingHide();
                        this.$axiosHandleError(err);
                    })
                    .then(() => {

                    });
            },
            uploadFile(id){
                let that = this;
                this.dropzoneFile.on('processing', function(){
                    this.options.url = urlApi+`transaction/upload/${id}`;
                });
                this.dropzoneFile.processQueue();
                this.dropzoneFile.on('success', function(){
                    that.$pageLoadingHide();
                    Swal.fire('Berhasil', 'Pengajuan transaksi dan file berhasil disimpan.','success').then(()=>{
                        that.$router.push({name: 't-pengajuan'});
                    });
                });
            }
        },
        computed: {
            countTotal(){
                return 10000;
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
