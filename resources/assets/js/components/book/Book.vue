<template>
  <div class="item">
    <bookDetails
      v-if="!editing" 
      :id="id"
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
      :cancel="cancelEdit"
      v-on:update="update" 
    />
    <tags 
      v-if="!editing"
      :tags="book.tags" 
      :bookId="book.id"
    />
  </div>  
</template>

<script>
import BookDetails from './BookDetails.vue';
import BookEdit from './BookEdit.vue';
import Tags from '../tag/Tags.vue';

export default {
  components: { BookDetails, BookEdit, Tags },

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