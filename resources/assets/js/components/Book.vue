<template>
    <div>
        <i v-show="loading" class="fa fa-spinner fa-spin"></i>
        <div class="bookElement" v-if="!editing">
            <div class="bookDetails">
                <a :href="notesLink">{{ title }}</a><br>
                {{ authorFirstName }}
                {{ authorLastName }}
            </div>
            <div class="bookControls">
                <i class="fa fa-edit" @click="edit"></i>
            </div>
        </div>
        <div class="bookEdit" v-if="editing">
            <input type="text" placeholder="Title" v-model="title" class="form-control">
            <div class="row">
                <div class="form-group col-md-6">
                    <input type="text" placeholder="Author first name" v-model="authorFirstName" class="form-control">
                </div>
                <div class="form-group col-md-6">
                    <input type="text" placeholder="Author last name" v-model="authorLastName" class="form-control">
                </div>
            </div>
            <button @click="submit" v-text="submitting ? 'Submitting' : 'Submit'" class="btn btn-primary"></button>
            <button @click="cancelEdit" class="btn btn-default">Cancel</button>
        </div>
    </div>  
</template>

<script>
    export default {

        props: ['id'],

        data() {
            return {
                title: '',
                authorFirstName: '',
                authorLastName: '',
                loading: true,
                editing: false,
                submitting: false
            }
        },

        computed: {
            notesLink() {
                return '/books/' + this.id + '/notes';
            }
        },

        mounted() {
            let that = this;
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post('getBookDetails', { 'id': this.id }, function(data) {
                that.title = data.title;
                that.authorFirstName = data.authorFirstName;
                that.authorLastName = data.authorLastName;
                that.loading = false;
            });
        },

        methods: {
            edit() {
                this.editing = true;
            },
            cancelEdit() {
                this.editing = false;
            },
            submit() {
                let that = this;
                this.submitting = true;
                $.post('storeBookDetails', {
                    'id': this.id,
                    'title': this.title,
                    'authorFirstName': this.authorFirstName,
                    'authorLastName': this.authorLastName
                }, function(data) {
                    that.editing = false;
                    that.submitting = false;
                });
            }
        }

    }
</script>

<style>

    .bookElement {
        clear: left;
        overflow: auto;        
        width: 100%;
        border-bottom: 1px solid #DEDEDE;
        margin-bottom: 10px;
        padding-bottom: 10px;
    }

    .bookDetails {
        float: left;
        margin-right: 10px;
    }

    .bookControls {
        float: left;
    }

    .bookControls i {
        cursor: pointer;
    }

    .bookEdit {     
        width: 100%;
        border-bottom: 1px solid #DEDEDE;
        margin-bottom: 10px;
        padding-bottom: 10px;        
    }

    .bookEdit input {
        margin-bottom: 3px;
        padding-left: 6px;
        padding-right: 6px;
        color: #666;
    }

    /*.bookEdit input[type="text"] {
        padding: 5px 6px 5px 6px;
        border-radius: 3px;
        box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.35), 0px 1px 1px rgba(255, 255, 255, 0.4);
        border: 1px solid #999;
    }

    .bookEdit input[type="text"]:hover, input[type="text"]:focus {
        color: #888;
        border: 1px solid #08c;
        box-shadow: 0px 1px 0px rgba(255, 255, 255, 0.25),inset 0px 3px 6px rgba(0 ,0, 0, 0.25);
    }

    .bookEdit input[type="text"]:focus {
        box-shadow: none;
        border: 1px solid #08c;
        outline: none;
    }*/

</style>