<template>
    <div>
        <div class="dropdown">
            <input type="text" :value="value" class="form-select" @click="$emit('get-options')" data-bs-toggle="dropdown" readonly>
            <ul class="dropdown-menu" style="width:100%;">
                <li class="p-4">
                    <input type="search" v-model="searchValue" @input="search()" class="form-control p-2" placeholder="Search">
                </li>
                <template v-if="loading">
                    <li>
                        <a class="dropdown-item dropdown-item-selectku-dewe" href="javascript:;">{{ loadingLabel }}</a>
                    </li>
                </template>
                <template v-else>
                    <li v-for="(context, index) in optionsLimit" v-if="context?.html">
                        <a @click="setValue(context)" class="dropdown-item dropdown-item-selectku-dewe" href="javascript:;" v-html="context?.html"></a>
                    </li>
                    <li v-for="(context, index) in optionsLimit" v-else>
                        <a @click="setValue(context)" class="dropdown-item dropdown-item-selectku-dewe" href="javascript:;" v-text="context?.text"></a>
                    </li>
                </template>
            </ul>
        </div>
    </div>
</template>

<script>
    import Lodash from 'lodash';
    export default {
        props: {
            modelValue: {
                type: String,
            },
            options: {
                type: Array,
                default: []
            },
            loading: {
                type: Boolean,
                default: false
            },
            loadingLabel: {
                type: String,
                default: 'Loading...'
            },
            serverSide: {
                type: Boolean,
                default: false
            },
            limit: {
                type: Number,
                default: 7
            }
        },
        data(){
            return {
                value: '',
                searchValue: '',
                triggerSearch: false
            }
        },
        watch: {
            value: {
                handler(val){
                },
            }
        },
        mounted(){
            let that = this;
        },
        methods: {
            setValue(value){
                // $event.target.value hanya untuk mengambil value dari input yang di emit tersebut
                this.$emit('update:modelValue', value);//modelValue merepresentasikan v-model di parent dan di set ke value
                this.$emit('change-options', value);
                this.value = value.html ? value?.html : value?.text;
            },
            search: Lodash.debounce(function($event){
                this.$emit('get-options',this.searchValue, this.limit);
            }, 1000),
            // search(){
            //     let that = this;
            //     // if(!this.triggerSearch){
            //     //     this.triggerSearch = true;
            //     //     setTimeout(function(){
            //     //         that.triggerSearch = false;
            //     //         console.log(that.searchValue);
            //     //     }, 1000);
            //     // }
            // },
        },
        computed: {
            optionsLimit(){
                return this.options.slice(0,this.limit);
            },
        }
    }
</script>

<style scoped>
    .dropdown-item-selectku-dewe{
        line-height:27px;
        font-size:15px;
        color:rgb(103, 101, 101);
    }
    .dropdown-item-selectku-dewe:hover{
        color:#2c98db;
        background-color:#68c2fa43;
    }
</style>
