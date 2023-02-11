<template>
    <div>
        <input type="file" id="dimas-1" class="form-control">
        <input type="file" id="dimas-2" class="form-control">
        <button class="btn btn-success" id="button-dimas" @click="simpan()">simpan</button>
    </div>
</template>

<script>
export default{
    data(){
        return {
            token: localStorage.getItem('pomdes_token')
        }
    },
    mounted(){

    },
    methods:{
        simpan(){
            let data = [
                {
                    id: 1,
                    name: 'dimas 1',
                    file: $('#dimas-1')[0].files[0]
                },
                {
                    id: 2,
                    name: 'dimas 2',
                    file: $('#dimas-2')[0].files[0]
                },
            ];

            let formData = new FormData();
            formData.append(`data[0]['id']`,data[0]?.id);
            formData.append(`data[0]['name']`,data[0]?.name);
            formData.append(`data[0]['file']`,data[0]?.file);
            formData.append(`data[1]['id']`,data[1]?.id);
            formData.append(`data[1]['name']`,data[1]?.name);
            formData.append(`data[1]['file']`,data[1]?.file);

            $.ajax({
                type: 'POST',
                url: 'api/dimas',
                processData: false,
                contentType: false,
                headers: {
                    Authorization: 'Bearer '+this.token
                },
                data:formData,
                success: function(res){
                    console.log(res);
                },
                error: function(){

                }
            });
        }
    }
}
</script>
