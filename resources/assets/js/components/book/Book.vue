<template>
  <div class="item">
    <bookDetails
      v-if="!editing" 
      :title="title"
      :authorFirstName="authorFirstName"
      :authorLastName="authorLastName"
      v-on:edit="edit" 
    />
    <bookEdit 
      v-if="editing" 
      :title="title"
      :authorFirstName="authorFirstName"
      :authorLastName="authorLastName"
      :submitting="submitting"
      v-on:update="update" 
      v-on:cancel="cancelEdit"
    />
    <div class="ui hidden divider"></div>
    <tags 
      v-if="!editing"
      :tags="book.tags" 
      :bookId="book.id"
    />
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
    update(title, authorFirstName, authorLastName) {
      let that = this;
      this.submitting = true;
      $.post(
        "/storeBookDetails",
        {
          id: this.id,
          title: title,
          authorFirstName: authorFirstName,
          authorLastName: authorLastName
        },
        function(data) {
          that.editing = false;
          that.submitting = false;
          that.title = title;
          that.authorFirstName = authorFirstName;
          that.authorLastName = authorLastName;
        }
      );
    }
  }
};
</script>