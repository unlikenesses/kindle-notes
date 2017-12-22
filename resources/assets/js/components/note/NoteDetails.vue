<template>
  <div>
    <div class="right floated content">
      <button class="ui basic button" @click="edit">
        <i class="edit icon link"></i>
        Edit Note
      </button>
    </div>
    <div class="content">
      {{ noteText }}
    </div>
  </div>
</template>

<script>
export default {
  props: ["id", "noteText"],
  methods: {
    edit() {
      this.$emit('edit');
    }
  }
};
</script>