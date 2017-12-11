<template>
	<div style="margin-top:10px">
    <tag 
      v-for="tag in newTags" 
      :tag="tag"
      :deleteTag="deleteTag"
      :editing="editing"
    />
    <div class="ui left icon input mini" v-if="editing">
      <i class="tags icon"></i>
      <input 
        type="text" 
        v-model.trim="newTag" 
        placeholder="Add a tag" 
        v-on:keyup="autoComplete"
        v-on:keyup.enter="submit"
      >
    </div>
		<a class="ui label" @click="toggleEditing()" v-if="editing">
			Done
		</a>
    <div class="ui fluid vertical menu" v-if="editing && autocompleteTags.length">
      <a 
        class="item" 
        v-for="result in autocompleteTags" 
        @click="selectTag(result)"
      >
        {{ result.tag }}
      </a>
    </div>
    <!-- <div class="ui search">
      <input class="prompt" type="text" placeholder="Add a tag" v-model="newTag">
      <div class="results"></div>
    </div> -->
		<a class="ui basic label" @click="toggleEditing()" v-if="!editing">
			<i class="fitted edit icon"></i>
			Edit Tags
		</a>
    <div class="ui red message" v-if="editing && tagTooLong">
      Tags can be a maximum of 32 characters long.
    </div>
    <!-- <p>New tag: <strong>{{ newTag }}</strong><br>AC length: <strong>{{autocompleteTags.length}}</strong></p> -->
	</div>
</template>

<script>
export default {
  props: ["tags", "bookId"],
  data() {
    return {
      newTags: this.tags,
      editing: false,
      newTag: null,
      tagTooLong: false,
      autocompleteTags: []
    };
  },
  methods: {
    toggleEditing() {
      this.editing = !this.editing;
      if (!this.editing) {
        // Add the entered tag if it exists:
        if (this.newTag && !this.tagTooLong) {
          this.addTag(this.newTag);
        }
        this.clearAll();
      }
    },
    submit() {
      this.autocompleteTags = [];
      if (this.newTag && !this.tagTooLong) {
        this.addTag(this.newTag);
        this.newTag = null;
      }
    },
    autoComplete(event) {
      var that = this;
      this.autocompleteTags = [];
      if (event.key == "Escape") {
        this.editing = false;
        this.clearAll();
      } else if (event.key != "Enter" && this.newTag && this.newTag.length > 2) {
        if (this.newTag.length > 32) {
          this.tagTooLong = true;
        } else {
          this.tagTooLong = false;
        }
        $.get('/tagAutoComplete', {
          'tag': this.newTag 
        }, function(data) {
          that.autocompleteTags = data;
        });
      }
    },
    addTag(tag) {
      if (!this.tagTooLong) {
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
    },
    selectTag(tag) {
      this.newTag = tag.tag;
      this.addTag(this.newTag);
      this.clearAll();
    },
    clearAll() {
      this.newTag = null;
      this.autocompleteTags = []; 
      this.tagTooLong = false;
    }
  }
};
</script>