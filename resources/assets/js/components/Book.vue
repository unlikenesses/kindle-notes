<template>
    <div class="item">
        <div class="bookElement" v-if="!editing">
            <div class="right floated content">
                <button class="ui basic button" @click="edit">
                <!-- <button class="ui basic button" @click="showModal"> -->
                    <i class="edit icon link"></i>
                    Edit Details
                </button>
            </div>
            <div class="content">
                <a :href="notesLink" class="header">{{ title }}</a>
                <div class="description">
                {{ authorFirstName }}
                {{ authorLastName }}
                </div>
            </div>
        </div>
        <div class="bookEdit" v-if="editing">
            <div class="ui equal width form">
                <div class="fields">
                    <div class="field">
                        <label>Title</label>
                        <input type="text" placeholder="Title" v-model="title">
                    </div>
                </div>
                <div class="fields">
                    <div class="field">
                        <label>First name</label>
                        <input 
                            type="text" 
                            placeholder="Author first name" 
                            v-model="authorFirstName"
                        >
                    </div>
                    <div class="field">
                        <label>Last name</label>
                        <input 
                            type="text" 
                            placeholder="Author last name" 
                            v-model="authorLastName"
                        >
                    </div>
                </div>
                <button 
                    @click="submit" 
                    v-text="submitting ? 'Updating' : 'Update'" 
                    v-bind:class="{'ui button blue': true, 'loading': submitting}"
                >
                </button>
            </div>
        </div>
        <tags :tags="book.tags" :bookId="book.id" v-if="!editing" />
    </div>  
</template>

<script>
export default {
  props: ["book", "tags"],

  data() {
    return {
      id: this.book.id,
      title: this.book.title,
      authorFirstName: this.book.author_first_name,
      authorLastName: this.book.author_last_name,
      loading: true,
      editing: false,
      submitting: false
    };
  },

  computed: {
    notesLink() {
      return "/books/" + this.id + "/notes";
    }
  },

  mounted() {
    $.ajaxSetup({
      headers: {
        "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content")
      }
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
      $.post(
        "storeBookDetails",
        {
          id: this.id,
          title: this.title,
          authorFirstName: this.authorFirstName,
          authorLastName: this.authorLastName
        },
        function(data) {
          that.editing = false;
          that.submitting = false;
        }
      );
    }
  }
};
</script>

<style>
.bookEdit {
  width: 100%;
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