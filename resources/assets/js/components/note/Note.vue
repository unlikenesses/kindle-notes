<template>
  <div>
    <div class="event">
      <div class="label">
        <i class="pencil icon"></i>
      </div>
      <div class="content">
        <div class="text">
          <noteDetails
            v-if="!editing" 
            :id="id"
            :noteText="noteText"
            v-on:edit="edit" 
          />
          <noteEdit 
            v-if="editing" 
            :noteText="noteText"
            :submitting="submitting"
            :cancel="cancelEdit"
            :errorMsg="errorMsg"
            v-on:update="update" 
          />
        </div>
        <div class="meta">
          <small>{{ date }}</small>
          <small v-if="page != ''">Page: {{ page}}</small>
          <small v-if="location != ''">Location: {{ location}}</small>
          <small>({{ type }})</small>
        </div>
      </div>
    </div>
    <hr>
  </div>  
</template>

<script>
import NoteDetails from './NoteDetails.vue';
import NoteEdit from './NoteEdit.vue';

export default {
  components: { NoteDetails, NoteEdit },

  props: ["note"],

  computed: {
    type() {
      return this.note.type == 1 ? 'Highlight' : 'Note';
    }
  },

  data() {
    return {
      id: this.note.id,
      noteText: this.note.note,
      page: this.note.page,
      location: this.note.location,
      date: this.note.date,
      loading: true,
      editing: false,
      submitting: false,
      errorMsg: null
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
      this.errorMsg = null;
      this.editing = false;
    },
    update(note) {
      if (note.length < 1) {
        this.errorMsg = 'Please enter some text.';
        return;
      }
      let that = this;
      this.errorMsg = null;
      this.submitting = true;
      $.post(
        '/notes/' + this.id + '/update',
        {
          note: note
        },
        function(data) {
          that.editing = false;
          that.submitting = false;
          that.noteText = note;
        }
      ).fail(function(error) {
        that.errorMsg = error.responseJSON.errors.note[0];
        that.submitting = false;
        // console.log('error', error);
      });
    }
  }
};
</script>