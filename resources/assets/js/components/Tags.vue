<template>
	<div v-if="numTags > 0">
		<span class="ui blue label" v-for="tag in newTags">
			<a :href="'/tag/' + tag.slug">{{ tag.tag }}</a>
			<i class="delete icon" @click="deleteTag(tag)" v-if="editing"></i>
		</span>
    <div class="ui left icon input mini" v-if="editing">
      <i class="tags icon"></i>
      <input type="text" v-model="newTag" placeholder="Add a tag" v-on:keyup.enter="submit">
    </div>
		<a class="ui label" @click="toggleEditing()" v-if="!editing">
			<i class="fitted edit icon"></i>
			Edit Tags
		</a>
		<a class="ui label" @click="toggleEditing()" v-if="editing">
			<i class="fitted edit icon"></i>
			Finished
		</a>
	</div>
</template>

<script>
export default {
  props: ["tags", "bookId"],
  data() {
    return {
      newTags: this.tags,
      editing: false,
      newTag: null
    };
  },
  computed: {
    numTags: function() {
      return this.newTags.length;
    }
  },
  methods: {
    toggleEditing() {
      this.editing = !this.editing;
    },
    submit() {
      this.addTag(this.newTag);
      this.newTag = null;
    },
    addTag(tag) {
      if (tag.length < 128) {
        var that = this;
        $.post("/addTagPivot", {
          book_id: this.bookId,
          tag: tag
        }).then(function() {
          that.getTags();
        });
      }
    },
    getTags() {
      var that = this;
      $.post(
        "/getTagsForBook",
        {
          book_id: this.bookId
        },
        function(data) {
          that.newTags = data;
        }
      );
    },
    deleteTag(item) {
      var that = this;
      $.post(
        "/deleteTagPivot",
        {
          book_id: item.pivot.book_id,
          tag_id: item.pivot.tag_id
        },
        function(data) {
          let i = that.newTags.indexOf(item);
          that.newTags.splice(i, 1);
        }
      );
    }
  }
};
</script>

<style>
ul {
  list-style-type: none;
  margin: 0;
  padding: 0;
}
li {
  display: inline-block;
  margin-right: 5px;
}
li a.pill {
  background: #999;
  color: white;
  font-size: 12px;
  font-weight: bold;
  padding: 2px 7px;
  border-radius: 5px;
  text-decoration: none;
}
li a.pill:hover {
  background: #666;
}
a.deleteTag {
  color: #666;
  margin: 0 2px 0 3px;
  font-weight: bold;
  font-size: 16px;
  text-decoration: none;
}
.form-inline {
  margin: 20px 0 10px 0;
}
</style>