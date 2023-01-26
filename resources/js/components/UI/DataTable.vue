<template>
    <div style="overflow-x:auto;">
        <input type="search" class="form-control form-control-solid" style="width:25%;" placeholder="Search">
        <table class="table my-4">
            <thead>
                <tr>
                    <th v-for="(val, i) in tableConfig?.data?.header">
                        <div class="d-flex justify-content-between" @click="val?.sort ? clickSort(val, i) : ''" :style="val?.sort ? 'cursor:pointer;' : ''">
                            <h5 class="text-gray-700">{{ val?.text }}</h5>
                            <template v-if="val?.sort && val?.activeSort">
                                <span v-if="tableConfig?.config?.orderBy == 'asc'"><i class="bi bi-sort-alpha-down fa-lg"></i></span>
                                <span v-if="tableConfig?.config?.orderBy == 'desc'"><i class="bi bi-sort-alpha-up fa-lg"></i></span>
                            </template>
                        </div>
                    </th>
                </tr>
            </thead>
            <tbody>
                <template v-if="tableConfig?.config?.loading">
                    <tr>
                        <td :colspan="tableConfig?.data?.header?.length">
                            <center class="p-5" style="display:block;">
                                <app-loader></app-loader>
                            </center>
                        </td>
                    </tr>
                </template>
                <template v-else>
                    <tr>
                        <td>asdfafdasdfd asefav sfaf aef wea fa wf af d s f s def a fea s a a a a a</td>
                        <td>asdfafdasdfd asefav sfaf aef wea fa wf af d s f s def a fea s a a a a a</td>
                        <td>asdfafdasdfd asefav sfaf aef wea fa wf af d s f s def a fea s a a a a a</td>
                        <td>asdfafdasdfd asefav sfaf aef wea fa wf af d s f s def a fea s a a a a a</td>
                        <td>asdfafdasdfd asefav sfaf aef wea fa wf af d s f s def a fea s a a a a a</td>
                        <td>asdfafdasdfd asefav sfaf aef wea fa wf af d s f s def a fea s a a a a a</td>
                    </tr>
                    <tr>
                        <td>asdfafdasdfd asefav sfaf aef wea fa wf af d s f s def a fea s a a a a a</td>
                        <td>asdfafdasdfd asefav sfaf aef wea fa wf af d s f s def a fea s a a a a a</td>
                        <td>asdfafdasdfd asefav sfaf aef wea fa wf af d s f s def a fea s a a a a a</td>
                        <td>asdfafdasdfd asefav sfaf aef wea fa wf af d s f s def a fea s a a a a a</td>
                        <td>asdfafdasdfd asefav sfaf aef wea fa wf af d s f s def a fea s a a a a a</td>
                        <td>asdfafdasdfd asefav sfaf aef wea fa wf af d s f s def a fea s a a a a a</td>
                    </tr>
                    <tr>
                        <td>asdfafdasdfd asefav sfaf aef wea fa wf af d s f s def a fea s a a a a a</td>
                        <td>asdfafdasdfd asefav sfaf aef wea fa wf af d s f s def a fea s a a a a a</td>
                        <td>asdfafdasdfd asefav sfaf aef wea fa wf af d s f s def a fea s a a a a a</td>
                        <td>asdfafdasdfd asefav sfaf aef wea fa wf af d s f s def a fea s a a a a a</td>
                        <td>asdfafdasdfd asefav sfaf aef wea fa wf af d s f s def a fea s a a a a a</td>
                        <td>asdfafdasdfd asefav sfaf aef wea fa wf af d s f s def a fea s a a a a a</td>
                    </tr>
                    <tr>
                        <td>asdfafdasdfd asefav sfaf aef wea fa wf af d s f s def a fea s a a a a a</td>
                        <td>asdfafdasdfd asefav sfaf aef wea fa wf af d s f s def a fea s a a a a a</td>
                        <td>asdfafdasdfd asefav sfaf aef wea fa wf af d s f s def a fea s a a a a a</td>
                        <td>asdfafdasdfd asefav sfaf aef wea fa wf af d s f s def a fea s a a a a a</td>
                        <td>asdfafdasdfd asefav sfaf aef wea fa wf af d s f s def a fea s a a a a a</td>
                        <td>asdfafdasdfd asefav sfaf aef wea fa wf af d s f s def a fea s a a a a a</td>
                    </tr>
                    <tr>
                        <td>asdfafdasdfd asefav sfaf aef wea fa wf af d s f s def a fea s a a a a a</td>
                        <td>asdfafdasdfd asefav sfaf aef wea fa wf af d s f s def a fea s a a a a a</td>
                        <td>asdfafdasdfd asefav sfaf aef wea fa wf af d s f s def a fea s a a a a a</td>
                        <td>asdfafdasdfd asefav sfaf aef wea fa wf af d s f s def a fea s a a a a a</td>
                        <td>asdfafdasdfd asefav sfaf aef wea fa wf af d s f s def a fea s a a a a a</td>
                        <td>asdfafdasdfd asefav sfaf aef wea fa wf af d s f s def a fea s a a a a a</td>
                    </tr>
                </template>
            </tbody>
        </table>
        <div class="control-bottom d-flex justify-content-between w-100">
            <div class="wrap-limit">
                <select class="form-select-solid form-select form-select-sm">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
            <div class="wrap-pagination">
                <ul class="pagination">
                    <li class="page-item previous disabled"><a href="javascript:;" class="page-link"><i class="previous"></i></a></li>
                    <li class="page-item "><a href="javascript:;" class="page-link">1</a></li>
                    <li class="page-item active"><a href="javascript:;" class="page-link">2</a></li>
                    <li class="page-item "><a href="javascript:;" class="page-link">3</a></li>
                    <li class="page-item "><a href="javascript:;" class="page-link">4</a></li>
                    <li class="page-item "><a href="javascript:;" class="page-link">5</a></li>
                    <li class="page-item "><a href="javascript:;" class="page-link">...</a></li>
                    <li class="page-item "><a href="javascript:;" class="page-link">10</a></li>
                    <li class="page-item next"><a href="javascript:;"  class="page-link"><i class="next"></i></a></li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script>
    import Lodash from 'lodash';
    export default {
        props: {

        },
        data(){
            return {
                tableConfig: {
                    data: {
                        header: [
                            {
                                text: 'No',
                                sortBy: 'id',
                                sort: false,
                                class: '',
                                style: ''
                            },
                            {
                                text: 'Nama',
                                sortBy: 'name',
                                sort: true,
                                class: '',
                                style: ''
                            },
                            {
                                text: 'No',
                                sortBy: 'id',
                                sort: true,
                                class: '',
                                style: ''
                            },
                            {
                                text: 'No',
                                sortBy: 'id',
                                sort: false,
                                class: '',
                                style: ''
                            },
                            {
                                text: 'No',
                                sortBy: 'id',
                                sort: false,
                                class: '',
                                style: ''
                            },
                            {
                                text: 'No',
                                sortBy: 'id',
                                sort: true,
                                class: '',
                                style: ''
                            },
                        ],
                        body: []
                    },
                    config: {
                        orderBy: 'asc',
                        sortBy: 'id',
                        loading: true
                    }
                }
            }
        },
        mounted(){

        },
        methods: {
            clickSort(val, i){
                $.each(this.tableConfig?.data?.header, function(index,value){
                    value.activeSort = false;
                });
                this.tableConfig.data.header[i].activeSort = true;
                if(this.tableConfig.config.orderBy == 'asc'){
                    this.tableConfig.config.orderBy = 'desc';
                    this.tableConfig.config.sortBy = val?.sortBy;
                }
                else if(this.tableConfig.config.orderBy == 'desc'){
                    this.tableConfig.config.orderBy = 'asc';
                    this.tableConfig.config.sortBy = val?.sortBy
                }
            }
        }
    }
</script>

<style scoped>
    table tr td,table tr th{
        border-bottom:1px dashed lightgray !important;
        padding:20px;
    }
    thead{
        border-bottom:2px solid lightgray;
    }
</style>
