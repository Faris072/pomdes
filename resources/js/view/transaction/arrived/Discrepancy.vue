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
                                    <h1>Form Ketidaksesuaian BBM</h1>
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
                                            <h2 class="text-warning">Form Ketidaksesuaian</h2>
                                            <br><br>
                                            <div class="form-ketidaksesuaian">
                                                <label for="name"><h5>Nama Laporan Ketidaksesuaian</h5></label>
                                                <input v-model="form.name" type="text" id="name" class="form-control" placeholder="Masukkan nama laporan ketidaksesuaian">
                                                <br>
                                                <label for="descripion"><h5>Deskripsi</h5></label>
                                                <textarea v-model="form.description" id="description" rows="5" class="form-control" placeholder="Masukkan deskripsi laporan ketidaksesuaian"></textarea>
                                            </div>
                                            <br><br>
                                            <div class="card" style="border:1px solid gold;">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <h4>Pilih BBM yang Tidak Sesuai</h4>
                                                    </div>
                                                    <br>
                                                    <div class="table-ketidaksesuaian">
                                                        <table style="width:100%;" class="table table-bordered">
                                                            <thead style="border-bottom:2px solid gray;">
                                                                <tr>
                                                                    <th class="text-center"><h5>Pilihan</h5></th>
                                                                    <th><h5>Jenis BBM</h5></th>
                                                                    <th><h5>Tipe Ketidakesuaian</h5></th>
                                                                    <th><h5>Ketidaksesuaian (Liter)</h5></th>
                                                                    <th><h5>Ketidaksesuaian (Rp)</h5></th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <tr v-for="(context, index) in form.fuelDiscrepancy">
                                                                    <td valign="middle">
                                                                        <div class="form-check form-check-custom form-check-solid justify-content-center">
                                                                            <input v-model="context.isDiscrepancy" class="form-check-input text-center" type="checkbox" :checked="context?.isDiscrepancy" />
                                                                        </div>
                                                                    </td>
                                                                    <td valign="middle">{{context?.name}}</td>
                                                                    <td valign="middle">
                                                                        <app-select2 v-model="context.discrepancy_type_id" :disabled="!context?.isDiscrepancy" :options="selectList.type.list" :loading="selectList.type.loading" @get-options="getDiscrepancyType" placeholder="Pilih tipe ketidaksesuaian" :multiple="false"></app-select2>
                                                                    </td>
                                                                    <td valign="middle">
                                                                        <app-money3 v-model="context.discrepancy_volume" class="form-control" placeholder="Isi volume ketidaksesuaian BBM" v-bind="money3" :disabled="!context?.isDiscrepancy" @keyup="calculateDiscrepancyPrice(index, $event)"></app-money3>
                                                                    </td>
                                                                    <td valign="middle">
                                                                        <app-money3 v-model="context.discrepancy_price" class="form-control" placeholder="Isi harga ketidaksesuaian BBM" v-bind="money3" :disabled="!context?.isDiscrepancy" @keyup="calculateDiscrepancyVolume(index, $event)"></app-money3>
                                                                    </td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                    <br><br>
                                                    <div class="total">
                                                        <h4>Total ketidaksesuaian Pesanan</h4>
                                                        <br>
                                                        <div class="row my-2">
                                                            <div class="col-md-3">
                                                                <h5 class="text-gray-700">Total Harga : </h5>
                                                            </div>
                                                            <div class="col-md-9">
                                                                <h5>Rp{{ $rupiahFormat(countFuelDiscrepancyPrice) }}</h5>
                                                            </div>
                                                        </div>
                                                        <div class="row my-2">
                                                            <div class="col-md-3">
                                                                <h5 class="text-gray-700">Total Volume : </h5>
                                                            </div>
                                                            <div class="col-md-9">
                                                                <h5>{{ $rupiahFormat(countFuelDiscrepancyVolume) }} Liter</h5>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <br><br>
                                    <div class="file">
                                        <h3 class="text-warning">Bukti Ketidaksesuaian</h3>
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
                        </div>
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
                dropzoneFile: '',
                loading : false,
                selectList: {
                    type: {
                        loading: false,
                        list: []
                    },
                },
                informasi: {
                    namaPemesan: '',
                    nama: '',
                    tanggal: '',
                    deskripsi: '',
                    bbm: []
                },
                form: {
                    name: '',
                    description: '',
                    price: '',
                    volume: '',
                    fuelDiscrepancy: [
                        {isDiscrepancy: false, name:'', fuel_transaction_id: '', discrepancy_type_id: '', discrepancy_volume: '', discrepancy_price: '', volume: '', price: '', limitVolume: '', limitPrice:''},
                    ],
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
                if(this.dropzoneFile){
                    this.dropzoneFile.destroy();
                }
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
                        this.informasi = {
                            namaPemesan: data?.user,
                            nama: data?.name,
                            tanggal: data?.created_at,
                            deskripsi: data?.description,
                            supplier: data?.fuel_transactions ? data?.fuel_transactions[0]?.fuel?.supplier?.username : '-'
                        };

                        this.informasi.bbm = [];
                        $.each(data?.fuel_transactions, function(i,val){
                            that.informasi.bbm.push({
                                jenis: val?.fuel?.name,
                                volume: val?.volume,
                                harga: val?.price
                            });
                        });

                        this.form.fuelDiscrepancy = [];
                        $.each(data?.fuel_transactions, function(i,val){
                            that.form.fuelDiscrepancy.push({
                                isDiscrepancy: false,
                                name: val?.fuel?.name,
                                fuel_transaction_id: val?.id,
                                discrepancy_type_id: '',
                                discrepancy_volume: '',
                                discrepancy_price: '',
                                volume: Number(val?.fuel?.volume).toFixed(2),
                                price: Number(val?.fuel?.price).toFixed(2),
                                limitVolume: Number(val?.volume).toFixed(2),
                                limitPrice: Number(val?.price).toFixed(2),
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
                    name: '',
                    description: '',
                    price: '',
                    volume: '',
                    fuelDiscrepancy: [
                        {isDiscrepancy: false, name:'', fuel_transaction_id: '', discrepancy_type_id: '', discrepancy_volume: '', discrepancy_price: '', volume: '', price: '', limitVolume: '', limitPrice:''},
                    ]
                }
            },
            simpan(){
                let that = this;
                console.log(this.form);
                let data = {
                    name: this.form.name,
                    description: this.form.description,
                    price: this.form.price,
                    volume: this.form.volume,
                    fuel_discrepancy: [],
                };

                $.each(this.form.fuelDiscrepancy, function(i, val){
                    if(val?.isDiscrepancy){
                        data.fuel_discrepancy.push({
                            fuel_transaction_id: val?.fuel_transaction_id,
                            discrepancy_type_id: val?.discrepancy_type_id?.id,
                            discrepancy_volume: val?.discrepancy_volume,
                            discrepancy_price: val?.discrepancy_price,
                        });
                    }
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
            getDiscrepancyType(search, limit){
                let that = this;

                this.selectList.type.loading = true;
                this.$axios().get(`transaction/discrepancy/select-discrepancy-type?search=${search}&limit=${limit}`)
                    .then(res => {
                        let data = res?.data?.data;
                        this.selectList.type.list = [];
                        $.each(data, function(i,val){
                            that.selectList.type.list.push({id: val?.id, text: val?.name});
                        });
                    })
                    .catch(err => {
                        this.$axiosHandleError(err);
                    })
                    .then(() => {
                        this.selectList.type.loading = false;
                    });
            },
            calculateDiscrepancyPrice(index, e){
                if(this.form.fuelDiscrepancy[index].discrepancy_volume > this.form.fuelDiscrepancy[index].limitVolume && e.keyCode != 8){
                    toastr.warning('Volume tidak boleh melebihi volume yang telah dipesan.');
                    this.form.fuelDiscrepancy[index].discrepancy_volume = this.form.fuelDiscrepancy[index].limitVolume;
                    this.form.fuelDiscrepancy[index].discrepancy_price = this.form.fuelDiscrepancy[index].price * this.form.fuelDiscrepancy[index].discrepancy_volume;
                }
                else{
                    this.form.fuelDiscrepancy[index].discrepancy_price = this.form.fuelDiscrepancy[index].price * this.form.fuelDiscrepancy[index].discrepancy_volume;
                }
            },
            calculateDiscrepancyVolume(index, e){
                if(this.form.fuelDiscrepancy[index].discrepancy_price > this.form.fuelDiscrepancy[index].limitPrice && e.keyCode != 8){
                    toastr.warning('Volume tidak boleh melebihi volume yang telah dipesan.');
                    this.form.fuelDiscrepancy[index].discrepancy_price = this.form.fuelDiscrepancy[index].limitPrice;
                    this.form.fuelDiscrepancy[index].discrepancy_volume = this.form.fuelDiscrepancy[index].discrepancy_price / this.form.fuelDiscrepancy[index].price;
                }
                else{
                    this.form.fuelDiscrepancy[index].discrepancy_volume = this.form.fuelDiscrepancy[index].discrepancy_price / this.form.fuelDiscrepancy[index].price;
                }
            },
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
            countFuelDiscrepancyPrice(){
                let that = this;
                let total = 0;
                $.each(this.form.fuelDiscrepancy, function(i,val){
                    total+=Number(val?.discrepancy_price);
                });
                this.form.price = total;
                return total;
            },
            countFuelDiscrepancyVolume(){
                let that = this;
                let total = 0;
                $.each(this.form.fuelDiscrepancy, function(i,val){
                    total+=Number(val?.discrepancy_volume);
                });
                this.form.volume = total;
                return total;
            },
            total(){
                let total = this.countHargaBbm + this.countFuelDiscrepancy;
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
    .table-ketidaksesuaian table tr td{
        border-bottom:1px solid lightgray;
        padding:10px;
    }
</style>
