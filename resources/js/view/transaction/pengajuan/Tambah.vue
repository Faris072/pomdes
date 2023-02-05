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
                            <br>
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
                                    <label for="supplier"><h6>Pilih Supplier</h6></label>
                                    <app-select2 v-model="form.supplier" :options="selectList.supplier.list" :loading="selectList.supplier.loading" @get-options="getSupplier" placeholder="Pilih supplier" :multiple="false" @change-options="resetFuel()"></app-select2>
                                    <br>
                                    <div class="wrap-bbm">
                                        <div class="row my-5 align-items-end" v-for="(context,index) in form.fuel">
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
                                                <button class="btn btn-light-success" @click="form.fuel.push({fuel: '',volume: '',price: ''})" v-if="index == 0" :disabled="!form.supplier.id">Tambah</button>
                                                <button class="btn btn-light-danger" @click="form.fuel.splice(index,1)" v-else :disabled="!form.supplier.id">Hapus</button>
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
                selectList: {
                    fuel: {
                        loading: false,
                        list: []
                    },
                    supplier: {
                        loading: false,
                        list: []
                    },
                },
                form: {
                    supplier: '',
                    fuel: [
                        {
                            fuel: '',
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

        },
        methods: {
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
            resetFuel(){
                this.form.fuel = [
                    {
                        fuel: '',
                        volume: '',
                        price: ''
                    }
                ]
            },
            calculateFuelPrice(index){
                this.form.fuel[index].price = this.form.fuel[index].fuel.price * this.form.fuel[index].volume;
            },
            calculateFuelVolume(index){
                this.form.fuel[index].volume = this.form.fuel[index].price / this.form.fuel[index].fuel.price;
            },
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
