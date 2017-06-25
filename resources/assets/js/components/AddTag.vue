<template>
	<div class="addTag">
		<div class="form-inline">
			<div class="form-group row">
				<div class="col-md-12">
					<input 
						type="text" 
						v-model="newTag" 
						v-on:keyup="autoComplete"
						placeholder="Add a tag" 
						class="form-control"
						>
					<button @click="addTag" class="btn btn-primary">Add</button>
					<div class="panel-footer tagsResults" v-if="results.length">
						<ul class="list-group">
							<li class="list-group-item" v-for="result in results" @click="selectTag(result)">
								{{ result.tag }}
							</li>
						</ul>
					</div>
				</div>
			</div>
		</div>
	</div>
</template>

<script>
	export default {
		data() {
            return {
            	newTag: null,
            	results: []
            }
        },
        methods: {
            addTag() {
            	this.$emit('addTag', this.newTag);
            	this.newTag = null;
            	this.results = [];
            },
            autoComplete() {
            	var that = this;
            	this.results = [];
            	if (this.newTag.length > 2) {
            		$.get('/tagAutoComplete', {
            			'tag': this.newTag 
            		}, function(data) {
            			that.results = data;
            		});
            	}
            },
            selectTag(tag) {
            	this.newTag = tag.tag;
            	// this.addTag();
            }
        }
	}
</script>

<style>
	.tagsResults li {
		cursor: pointer;
	}
</style>