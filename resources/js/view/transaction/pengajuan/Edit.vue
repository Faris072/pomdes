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
                            <br>
                            <div class="d-flex justify-content-between align-items-center">
                                <h2>Form Pengajuan Pembelian BBM</h2>
                                <button class="btn btn-warning" @click="simpan()">Simpan Perubahan</button>
                            </div>
                            <br>
                            <div class="wrap-form">
                                <div class="form my-5" v-if="$store.state.auth.role_id == 1">
                                    <label for="user"><h5>User</h5></label>
                                    <app-select2 id="user" v-model="form.user" :options="selectList.user.list" :loading="selectList.user.loading" @get-options="getUser" placeholder="Pilih pomdes" :multiple="false" />
                                </div>
                                <div class="form my-5">
                                    <label for="name"><h5>Nama Transaksi</h5></label>
                                    <input v-model="form.name" type="text" class="form-control" placeholder="Masukkan nama transaksi">
                                </div>
                                <div class="form my-5">
                                    <label for="name"><h5>Deskripsi</h5></label>
                                    <textarea v-model="form.description" class="form-control" rows="5" placeholder="Masukkan deskripsi transaksi"></textarea>
                                </div>
                            </div>
                            <br>
                            <div class="card" style="border:1px solid gold;">
                                <div class="card-body">
                                    <h3 style="color:gold;">Bahan Bakar</h3>
                                    <br>
                                    <label for="supplier"><h6>Pilih Supplier</h6></label>
                                    <app-select2 v-model="form.supplier" :options="selectList.supplier.list" :loading="selectList.supplier.loading" @get-options="getSupplier" placeholder="Pilih supplier" :multiple="false" @change-options="resetFuel()"></app-select2>
                                    <br>
                                    <div class="wrap-bbm">
                                        <div class="row my-5 align-items-end" v-for="(context,index) in form.fuels">
                                            <div class="col-md-4">
                                                <h6>Jenis BBM</h6>
                                                <app-select2 v-model="context.fuel" :options="selectList.fuel.list" :loading="selectList.fuel.loading" @get-options="getFuel" placeholder="Pilih BBM" :multiple="false" :disabled="!form.supplier.id" @change-options="context.price = ''; context.volume = ''"></app-select2>
                                            </div>
                                            <div class="col-md-3">
                                                <h6>Volume BBM (Liter)</h6>
                                                <app-money3 v-model="context.volume" class="form-control" placeholder="Isi volume BBM" v-bind="money3" :disabled="!form.supplier.id || !context.fuel.id" @keyup="calculateFuelPrice(index)"></app-money3>
                                            </div>
                                            <div class="col-md-3">
                                                <h6>Harga BBM (Rp)</h6>
                                                <app-money3 v-model="context.price" class="form-control" placeholder="Isi harga BBM" v-bind="money3" :disabled="!form.supplier.id || !context.fuel.id" @keyup="calculateFuelVolume(index)"></app-money3>
                                            </div>
                                            <div class="col-md-2">
                                                <button class="btn btn-light-success" @click="form.fuels.push({fuel: '',volume: '',price: ''})" v-if="index == 0" :disabled="!form.supplier.id">Tambah</button>
                                                <button class="btn btn-light-danger" @click="form.fuels.splice(index,1)" v-else :disabled="!form.supplier.id">Hapus</button>
                                            </div>
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
                    user: '',
                    supplier: '',
                    name: '',
                    description: '',
                    fuels: [
                        {
                            fuel: '',
                            fuel_id: '',
                            volume: '',
                            price: ''
                        }
                    ]
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
                this.$axios().get(`transaction/${this.$route.params.id}`)
                    .then(res => {
                        let data = res?.data?.data;
                        console.log(data);
                        this.form.user = {id: data?.user?.id, text: data?.user?.username};
                        this.form.supplier = {id: data?.fuel_transactions[0]?.fuel?.supplier?.id, text: data?.fuel_transactions[0]?.fuel?.supplier?.username};
                        this.form.name = data?.name;
                        this.form.description = data?.description;
                        if(data?.fuel_transactions?.length > 0){
                            this.form.fuels = []
                        }
                        $.each(data?.fuel_transactions, function(i,val){
                            that.form.fuels.push({
                                fuel: {id: val?.fuel?.id, text: val?.fuel?.name},
                                fuel_id: '',
                                volume: val?.volume,
                                price: val?.price
                            });
                        });
                    })
                    .catch(err => {
                        this.$axiosHandleError(err);
                    })
                    .then(() => {

                    });
            },
            getFuel(search, limit){
                let that = this;

                this.selectList.fuel.loading = true;
                this.$axios().get(`fuel/select-list?search=${search}&limit=${limit}&user_id=${this.form?.supplier?.id}`)
                    .then(res => {
                        let data = res?.data?.data;
                        this.selectList.fuel.list = [];
                        $.each(data, function(i,val){
                            that.selectList.fuel.list.push({id: val?.id, text: val?.name, price: val?.price});
                        });
                    })
                    .catch(err => {
                        this.$axiosHandleError(err);
                    })
                    .then(() => {
                        this.selectList.fuel.loading = false;
                    });
            },
            getSupplier(search, limit){
                let that = this;

                this.selectList.supplier.loading = true;
                this.$axios().get(`fuel/select-supplier?search=${search}&limit=${limit}`)
                    .then(res => {
                        let data = res?.data?.data;
                        this.selectList.supplier.list = [];
                        $.each(data, function(i,val){
                            that.selectList.supplier.list.push({id: val?.id, text: val?.username});
                        });
                    })
                    .catch(err => {
                        this.$axiosHandleError(err);
                    })
                    .then(() => {
                        this.selectList.supplier.loading = false;
                    });
            },
            getUser(search, limit){
                let that = this;
                this.selectList.user.loading = true;

                let data = {
                    search,
                    limit
                };

                this.$axios().get(`users/select-pomdes`, {params: data})
                    .then(res => {
                        let data = res?.data?.data;
                        this.selectList.user.list = [];
                        $.each(data, function(i,val){
                            that.selectList.user.list.push({id: val?.id, text: val?.username});
                        });
                    })
                    .catch(err => {
                        this.$axiosHandleError(err);
                    })
                    .then(() => {
                        this.selectList.user.loading = false;
                    })
            },
            resetFuel(){
                this.form.fuels = [
                    {
                        fuel: '',
                        volume: '',
                        price: ''
                    }
                ]
            },
            calculateFuelPrice(index){
                this.form.fuels[index].price = this.form.fuels[index].fuel.price * this.form.fuels[index].volume;
            },
            calculateFuelVolume(index){
                this.form.fuels[index].volume = this.form.fuels[index].price / this.form.fuels[index].fuel.price;
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
