<template>
    <div>
        <span>ID = {{ id }}</span><br>
        <input type="text" name="title" :value="title"><br>
        <input type="text" name="author" :value="author"><br>
        <button @click="submit">Submit</button>
    </div>  
</template>

<script>
    export default {

        props: ['id'],

        data() {
            return {
                title: '',
                author: ''
            }
        },

        mounted() {
            let that = this;
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post('test', { 'id': this.id }, function(data) {
                // console.log('received ', data);
                that.title = data.title;
                that.author = data.author;
            });
        },

        methods: {
            submit() {
                console.log("Book.vue :25", 'submitting!');
            }
        }

    }
</script>