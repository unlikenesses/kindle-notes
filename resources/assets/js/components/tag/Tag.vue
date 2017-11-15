<template>
  <span class="ui blue label">
    <a :href="'/tag/' + tag.slug">{{ tag.tag }}</a>
    <i class="delete icon" @click="deleteTag(tag)" v-if="editing"></i>
  </span>
</template>

<script>
export default {
  props: ["tag", "editing"],
};
</script>