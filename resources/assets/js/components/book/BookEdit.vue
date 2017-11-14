<template>
  <div class="ui equal width form">
    <div class="fields">
      <div class="field">
        <label>Title</label>
        <input type="text" placeholder="Title" v-model="formTitle">
      </div>
    </div>
    <div class="fields">
      <div class="field">
        <label>First name</label>
        <input 
          type="text" 
          placeholder="Author first name" 
          v-model="formAuthorFirstName"
        >
      </div>
      <div class="field">
        <label>Last name</label>
        <input 
          type="text" 
          placeholder="Author last name" 
          v-model="formAuthorLastName"
        >
      </div>
    </div>
    <button 
      @click="submit" 
      v-text="submitting ? 'Updating' : 'Update'" 
      v-bind:class="{'ui button blue': true, 'loading': submitting}"
    >
    </button>
    <button
      @click="cancel"
      class="ui button"
    >
      Cancel
    </button>
  </div>
</template>

<script>
export default {
  props: ["title", "authorFirstName", "authorLastName", "submitting"],

  data() {
    return {
      formTitle: this.title,
      formAuthorFirstName: this.authorFirstName,
      formAuthorLastName: this.authorLastName,
      loading: true
    };
  },

  methods: {
    cancel() {
      this.$emit("cancel");
    },
    submit() {
      this.$emit(
        "update",
        this.formTitle,
        this.formAuthorFirstName,
        this.formAuthorLastName
      );
    }
  }
};
</script>