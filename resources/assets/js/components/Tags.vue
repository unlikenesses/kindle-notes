<template>
	<div class="tagsHolder container-fluid">
		<div class="row">
			<div class="col-md-12">
				<ul v-if="numTags > 0">
					<li v-for="tag in newTags">
						<a class="pill" :href="'/tag/' + tag.slug">
							{{ tag.tag }}
						</a>
						<a href="#" class="deleteTag" v-if="editing" @click="deleteTag(tag)">
							&times;
						</a>
					</li>
				</ul>
			</div>
		</div>
		<div class="addTag" v-if="editing">
			<div class="form-inline">
				<div class="form-group row">
					<div class="col-md-12">
						<input type="text" v-model="newTag" placeholder="Add a tag" class="form-control">
						<button @click="addTag" class="btn btn-primary">Add</button>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
	export default {

        props: ['tags', 'bookId', 'editing'],
        data() {
            return {
            	newTag: null,
            	newTags: this.tags
            }
        },
        computed: {
        	numTags: function() {
        		return this.newTags.length;
        	}
        },
        methods: {
            addTag() {
            	if (this.newTag.length < 128) {
            		var that = this;
            		$.post('/addTagPivot', {
	                    'book_id': this.bookId,
	                    'tag': this.newTag,
	                }).then(function() {
	                	that.getTags();
	                	that.newTag = null;
	                    // that.editing = false;
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
                    // that.editing = false;
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
	.tagsHolder li a.pill {
		background: #999;
		color: white;
		font-size: 12px;
		font-weight: bold;
		padding: 2px 7px;
		border-radius: 5px;
		text-decoration: none;
	}
	.tagsHolder li a.pill:hover {
		background: #666;
	}
	.tagsHolder a.deleteTag {
		color: #666;
		margin: 0 2px 0 3px;
		font-weight: bold;
		font-size: 16px;
		text-decoration: none;
	}
	.tagsHolder .form-inline {
		margin: 20px 0 20px 0;
	}
</style>