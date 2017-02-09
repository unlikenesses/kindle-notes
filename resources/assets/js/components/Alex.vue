<template>
    <h1 class="helloMessage">{{ message }}</h1>
</template>

<script>
    export default {
        props: {},

        data() {
            return {
                message: "Hello .vue component"
            }
        },

        ready() {}

    }
</script>