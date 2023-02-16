<template>
    <div>
        <div id="main-content">
            <div class="post d-flex flex-column-fluid" id="kt_post">
                <div id="kt_content_container" class="container-xxl">
                    <div class="d-flex justify-content-end">
                        <div class="card-header border-0 pt-5 align-items-center" style="justify-content:flex-end;">
                            <button class="btn btn-secondary" @click="$router.push({name: 't-tagihan'})"><i class="bi bi-arrow-left fa-lg"></i> Kembali</button>
                        </div>
                    </div>
                    <br>
                    <div class="card" v-show="loading">
                        <div class="card-body d-flex justify-content-center align-items-center">
                            <app-loader></app-loader>
                        </div>
                    </div>
                    <div class="card mb-5 mb-xl-8" v-show="!loading">
                        <div class="card-body container">
                            <div class="informasi my-5">
                                <div class="head d-flex justify-content-between align-items-center">
                                    <h1>Form Penerbitan Tagihan</h1>
                                    <button class="btn btn-warning" @click="simpan()">Simpan</button>
                                </div>
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
                                                    <h5 class="text-primary" style="cursor:pointer;" @click="detailProfile()">{{ informasi.namaPemesan.username }}</h5>
                                                </div>
                                            </div>
                                            <div class="row my-4">
                                                <div class="col-md-3">
                                                    <h5 class="text-gray-700">Nama Transaksi</h5>
                                                </div>
                                                <div class="col-md-9">
                                                    <h5>{{ informasi?.nama }}</h5>
                                                </div>
                                            </div>
                                            <div class="row my-4">
                                                <div class="col-md-3">
                                                    <h5 class="text-gray-700">Tanggal Pengajuan</h5>
                                                </div>
                                                <div class="col-md-9">
                                                    <h5>{{ $moment(informasi.tanggal).format('DD-MM-YYYY H:m:s') }}</h5>
                                                </div>
                                            </div>
                                            <div class="row my-4">
                                                <div class="col-md-3">
                                                    <h5 class="text-gray-700">Deskripsi</h5>
                                                </div>
                                                <div class="col-md-9">
                                                    <h5>{{ informasi.deskripsi }}</h5>
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
                                                                    <h5>Supplier: {{ informasi?.supplier }}</h5>
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
                                                                                <tr v-for="(context, index) in informasi.bbm">
                                                                                    <td class="text-center">{{ index+1 }}</td>
                                                                                    <td>{{ context?.jenis }}</td>
                                                                                    <td>{{ $rupiahFormat(context?.volume) }}</td>
                                                                                    <td>{{ $rupiahFormat(context?.harga)}}</td>
                                                                                </tr>
                                                                            </tbody>
                                                                            <tr>
                                                                                <td colspan="2"><b>Total</b></td>
                                                                                <td><b>{{ $rupiahFormat(countTotalVolume) }} Liter</b></td>
                                                                                <td><b>Rp{{ $rupiahFormat(countHargaBbm) }}</b></td>
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
                                                    <h5>Rp{{ $rupiahFormat(countHargaBbm) }}</h5>
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
                                                    <br>
                                                    <div class="row">
                                                        <div class="col-md-3"><h5 class="text-gray-700">Total Biaya Tambahan</h5></div>
                                                        <div class="col-md-9"><h5>Rp{{ $rupiahFormat(countAdditionalCosts) }}</h5></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <br>
                                            <div class="row">
                                                <div class="col-md-3">
                                                    <h5 class="text-gray-700">Total Biaya Transaksi</h5>
                                                </div>
                                                <div class="col-md-9">
                                                    <h4>Rp{{ $rupiahFormat(total) }}</h4>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <br><br>
                                    <div class="file">
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
                                    <br><br>
                                    <div class="preview-file" v-if="files?.length">
                                        <h3 class="text-warning">Preview File</h3>
                                        <div class="row my-5">
                                            <div class="col-md-3 my-3" v-for="(context, index) in files">
                                                <div class="card card-file">
                                                    <div @click="hapusFile(context?.id)" class="close" style="position: absolute !important; top:10px; right:10px; z-index:1000; cursor:pointer;"><i class="bi bi-x"></i></div>
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
                loading : false,
                files: [],
                informasi: {
                    namaPemesan: '',
                    nama: '',
                    tanggal: '',
                    deskripsi: '',
                    bbm: []
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
            this.initDropzone();
            this.resetForm();
            this.getData();
        },
        unmounted(){
            this.dropzoneFile.destroy();
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
            getData(){
                let that = this;
                this.loading = true;
                this.$axios().get(`transaction/${this.$route.params.id}`)
                    .then(res => {
                        let data = res?.data?.data;
                        console.log(data);
                        this.informasi = {
                            namaPemesan: data?.user,
                            nama: data?.name,
                            tanggal: data?.created_at,
                            deskripsi: data?.description,
                            supplier: data?.fuel_transactions ? data?.fuel_transactions[0]?.fuel?.supplier?.username : '-'
                        };

                        this.files = data?.invoice_pomdes?.invoice_pomdes_files;

                        this.informasi.bbm = [];
                        $.each(data?.fuel_transactions, function(i,val){
                            that.informasi.bbm.push({
                                jenis: val?.fuel?.name,
                                volume: val?.volume,
                                harga: val?.price
                            });
                        });

                        this.form.additionalCosts = [];
                        $.each(data?.invoice_pomdes?.additional_costs, function(i,val){
                            that.form.additionalCosts.push({
                                name: val?.name,
                                nominal: Number(val?.nominal).toFixed(2)
                            });
                        });
                    })
                    .catch(err => {
                        this.$axiosHandleError(err);
                    })
                    .then(() => {
                        this.loading = false;
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

                let data = {
                    transaction_id: this.$route.params.id,
                    total: this.form.total,
                    additional_costs: []
                };

                $.each(this.form?.additionalCosts, function(i,val){
                    data.additional_costs.push({name: val?.name, nominal: val?.nominal});
                });

                this.$pageLoadingShow();
                this.$axios().post(`transaction/invoice-pomdes`, data)
                    .then(res => {
                        if(this.dropzoneFile?.files?.length > 0){
                            this.uploadFile(res?.data?.data?.id);
                        }
                        else{
                            this.$pageLoadingHide();
                            Swal.fire('Berhasil', 'Penerbitan tagihan transaksi berhasil disimpan.','success').then(()=>{
                                that.$router.push({name: 't-tagihan'});
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
                    this.options.url = urlApi+`transaction/invoice-pomdes/file/${id}`;
                });
                this.dropzoneFile.processQueue();
                this.dropzoneFile.on('success', function(){
                    that.$pageLoadingHide();
                    Swal.fire('Berhasil', 'Penerbitan tagihan transaksi dan file berhasil disimpan.','success').then(()=>{
                        that.$router.push({name: 't-tagihan'});
                    });
                });
            },
            hapusFile(id){
                let that = this;

                Swal.fire({
                    title: `Hapus file yang dipilih?`,
                    html: `File akan dihapus dan tidak dapat dikembalikan lagi.`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Hapus',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#DB3700'
                }).then(result => {
                    if(result.isConfirmed){
                        this.$pageLoadingShow();
                        this.$axios().delete(`transaction/invoice-pomdes/file/${id}`)
                            .then(res => {
                                this.getData();
                                toastr.success('File berhasil dihapus');
                            })
                            .catch(err => {
                                this.$axiosHandleError(err);
                            })
                            .then(() => {
                                this.$pageLoadingHide();
                            });
                    }
                });
            }
        },
        computed: {
            countTotal(){
                return 10000;
            },
            countTotalVolume(){
                let that = this;
                let total = 0;
                $.each(this.informasi.bbm, function(i,val){
                    total+=Number(val?.volume);
                });
                return total;
            },
            countHargaBbm(){
                let that = this;
                let total = 0;
                $.each(this.informasi.bbm, function(i,val){
                    total+=Number(val?.harga);
                });
                return total;
            },
            countAdditionalCosts(){
                let that = this;
                let total = 0;
                $.each(this.form.additionalCosts, function(i,val){
                    total+=Number(val?.nominal);
                });
                return total;
            },
            total(){
                let total = this.countHargaBbm + this.countAdditionalCosts;
                this.form.total = total;
                return total;
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
