<template>
	<div class="tagsHolder">
		<ul v-if="numTags > 0">
			<li v-for="tag in newTags">
				<a :href="'tag/' + tag.slug">
					{{ tag.tag }}
				</a>
				<a href="#" v-if="editing" @click="deleteTag(tag)">
					x
				</a>
			</li>
		</ul>
		<div class="addTag" v-if="editing">
			<input type="text" v-model="newTag" placeholder="Add a tag" class="form-control">
			<button @click="addTag" class="btn btn-primary">Add</button>
		</div>
        <div class="tagsControls">
            <i class="fa fa-edit" @click="edit"></i>
        </div>
	</div>
</template>

<script>
	export default {

        props: ['tags', 'bookId'],
        data() {
            return {
            	editing: false,
            	newTag: null,
            	newTags: this.tags
            }
        },
        computed: {
        	numTags: function() {
        		return this.tags.length;
        	}
        },
        methods: {
            edit() {
                this.editing = !this.editing;
            },
            addTag() {
            	if (this.newTag.length < 128) {
            		var that = this;
            		$.post('/addTagPivot', {
	                    'book_id': this.bookId,
	                    'tag': this.newTag,
	                }).then(function() {
	                	that.getTags();
	                    that.editing = false;
	                });
            	}
            },
            getTags() {
            	var that = this;
            	$.post('/getTagsForBook', {
            		'book_id': this.bookId
            	}, function(data) {
            		that.newTags = data;
            	});
            },
            deleteTag(item) {
            	var that = this;
            	$.post('/deleteTagPivot', {
                    'book_id': item.pivot.book_id,
                    'tag_id': item.pivot.tag_id,
                }, function(data) {
                    that.editing = false;
                    let i = that.newTags.indexOf(item);
                    that.newTags.splice(i, 1);
                });
            }
        }
    }
</script>

<style>
	.tagsHolder {
		overflow: auto;
	}
	.tagsHolder ul {
		list-style-type: none; 
		margin: 0;
		padding: 0;
	}
	.tagsHolder li {
		display: inline-block;
		margin-right: 5px;
	}
	.tagsHolder li a {
		background: #999;
		color: white;
		font-size: 12px;
		font-weight: bold;
		padding: 2px 7px;
		border-radius: 5px;
		text-decoration: none;
	}
	.tagsHolder li a:hover {
		background: #666;
	}
	.tagsControls {
        display: inline-block;
    }

    .tagsControls i {
        cursor: pointer;
    }
</style>