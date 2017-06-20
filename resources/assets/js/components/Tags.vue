<template>
	<div class="tagsHolder">
		<ul v-if="numTags > 0">
			<li v-for="tag in tags">
				<a href="">
					{{ tag.tag }}
				</a>
			</li>
		</ul>
	</div>
</template>

<script>
	export default {

        props: ['tags'],
        mounted() {
        	// console.log(this.tags);
        },
        computed: {
        	numTags: function() {
        		return this.tags.length;
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