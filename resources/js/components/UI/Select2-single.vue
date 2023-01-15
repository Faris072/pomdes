<template>
    <div>
        <select :id="id" data-control="select2" :data-placeholder="placeholder" :options="options" class="form-select" :multiple="multiple ? 'multiple' : multiple">
            <option class="p-3" v-for="(val, i) in options" :value="options.id" v-if="options.html" v-html="val?.html"></option>
            <option class="p-3" v-for="(val, i) in options" :value="options.id" v-else v-text="val?.text"></option>
        </select>
    </div>
</template>

<script>
    export default {
        props: {
            name: '',
            id: '',
            options: {
                Array,
                default: []
            },
            value: null,
            multiple: {
                Boolean,
                default: false
            }
        },
        data(){
            return {
                select2data: []
            }
        },
        mounted(){
            console.log(this.options);
            $('#'+this.id).select2();
        },
        methods: {

        }
    }
</script>

<style scoped>
    #app {
        margin-top: 20px;
    }

    pre {
        margin-top: 20px;
    }
</style>
