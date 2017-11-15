<template>
  <div>
    <div class="right floated content">
      <button class="ui basic button" @click="edit">
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
</template>

<script>
export default {
  props: ["id", "title", "authorFirstName", "authorLastName"],
  computed: {
    notesLink() {
      return "/books/" + this.id + "/notes";
    }
  },
  methods: {
    edit() {
      this.$emit('edit');
    }
  }
};
</script>