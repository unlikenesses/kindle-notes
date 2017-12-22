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
    update(note) {
      let that = this;
      this.submitting = true;
      $.post(
        "/storeNoteDetails",
        {
          id: this.id,
          note: note
        },
        function(data) {
          that.editing = false;
          that.submitting = false;
          that.note = note;
        }
      );
    }
  }
};
</script>