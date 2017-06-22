<template>
	<div class="tagsHolder">
		<ul v-if="numTags > 0">
			<li v-for="tag in tags">
				<a :href="'tag/' + tag.slug">
					{{ tag.tag }}
				</a>
				<a href="#" v-if="editing" @click="deleteTag(tag)">
					x
				</a>
			</li>
		</ul>
        <div class="tagsControls">
            <i class="fa fa-edit" @click="edit"></i>
        </div>
	</div>
</template>

<script>
	export default {

        props: ['tags'],
        data() {
            return {
            	editing: false
            }
        },
        mounted() {
        	// console.log(this.tags);
        },
        computed: {
        	numTags: function() {
        		return this.tags.length;
        	}
        },
        methods: {
            edit() {
                this.editing = true;
            },
            deleteTag(item) {
            	// console.log(item.pivot);
            	var that = this;
            	$.post('/deleteTagPivot', {
                    'book_id': item.pivot.book_id,
                    'tag_id': item.pivot.tag_id,
                }, function(data) {
                    that.editing = false;
                    let i = that.tags.indexOf(item);
                    that.tags.splice(i, 1);
                });
            }
        }
    }
</script>

<style>
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
</style>