<template>
  <div class="ui equal width form">
    <div class="fields">
      <div class="field">
        <label>Note</label>
        <textarea v-model="formNoteText">
      </div>
    </div>
    <div class="alert alert-danger" v-if="errorMsg" v-text="errorMsg"></div>
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
  props: ["noteText", "submitting", "cancel", "errorMsg"],

  data() {
    return {
      formNoteText: this.noteText,
      loading: true
    };
  },

  methods: {
    submit() {
      this.$emit(
        "update",
        this.formNoteText
      );
    }
  }
};
</script>