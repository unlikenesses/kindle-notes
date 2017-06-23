<template>
	<div class="addTag">
		<div class="form-inline">
			<div class="form-group row">
				<div class="col-md-12">
					<input type="text" v-model="newTag" placeholder="Add a tag" class="form-control">
					<button @click="addTag" class="btn btn-primary">Add</button>
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
            }
        },
        methods: {
            addTag() {
            	this.$emit('addTag', this.newTag);
            	this.newTag = null;
            }
        }
	}
</script>