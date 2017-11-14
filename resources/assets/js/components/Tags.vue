<template>
	<div v-if="numTags > 0">
		<div class="ui hidden divider"></div>
		<ul>
			<li v-for="tag in newTags">
				<span class="ui blue label">
					<a :href="'/tag/' + tag.slug">{{ tag.tag }}</a>
					<i class="delete icon" @click="deleteTag(tag)"></i>
				</span>
			</li>
			<a class="ui blue label" @click="showAdd()">
				<i class="add icon"></i>
			</a>
		</ul>
		<div class="ui hidden divider"></div>
		<add-tag v-if="editing" v-on:addTag="addTag"></add-tag>
		<div class="ui hidden divider"></div>
	</div>
</template>

<script>
	export default {

        props: ['tags', 'bookId'],
        data() {
            return {
				newTags: this.tags,
				editing: false
            }
        },
        computed: {
        	numTags: function() {
        		return this.newTags.length;
        	}
        },
        methods: {
			showAdd() {
				this.editing = true;
			},
            addTag(tag) {
            	if (tag.length < 128) {
            		var that = this;
            		$.post('/addTagPivot', {
	                    'book_id': this.bookId,
	                    'tag': tag,
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
                    let i = that.newTags.indexOf(item);
                    that.newTags.splice(i, 1);
                });
            }
        }
    }
</script>

<style>
	.tagsHolder {
		/* overflow: auto; */
	}
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