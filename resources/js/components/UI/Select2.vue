<template>
    <div>
        <div class="dropdown">
            <input type="text" :data-value="modelValue" :value="value" class="form-select" data-bs-toggle="dropdown" readonly>
            <ul class="dropdown-menu" style="width:100%;">
                <li class="p-4">
                    <input type="search" class="form-control p-2" placeholder="Search">
                </li>
                <li v-for="(context, index) in options" v-if="context?.html">
                    <a @click="setValue(context)" class="dropdown-item dropdown-item-selectku-dewe" href="javascript:;" v-html="context?.html"></a>
                </li>
                <li v-for="(context, index) in options">
                    <a @click="setValue(context)" class="dropdown-item dropdown-item-selectku-dewe" href="javascript:;" v-text="context?.text"></a>
                </li>
            </ul>
        </div>
    </div>
</template>

<script>
    export default {
        props: {
            modelValue: {
                type: String,
            },
            options: {
                type: Array,
                default: []
            }
        },
        data(){
            return {
                value: ''
            }
        },
        watch: {
            value: {
                handler(val){
                    // this.$emit('update:modelValue', this.$event.target.value);
                    this.$emit('change-options', this.modelValue);
                },
            }
        },
        mounted(){

        },
        methods: {
            setValue(value){
                let el = this.$el;
                el.querySelector('input').dataValue = this.modelValue;
                this.value = value.html ? value?.html : value.text;
            }
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
