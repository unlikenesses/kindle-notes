<template>
  <div>
    <div class="right floated content">
      <button class="ui basic button" @click="editNote">
        <i class="edit icon link"></i>
        Edit Note
      </button>
    </div>
    <div class="right floated content">
      <button 
        v-bind:class="{'ui button basic': true, 'loading': deleting}"
        @click="deleteNote"
      >
        <i class="delete icon link"></i>
        Delete Note
      </button>
    </div>
    <div class="content">
      {{ noteText }}
    </div>
  </div>
</template>

<script>
export default {
  props: ["id", "deleting", "noteText"],

  methods: {
    editNote() {
      this.$emit('edit');
    },
    deleteNote() {
      this.$emit('delete');
    }
  }
};
</script>