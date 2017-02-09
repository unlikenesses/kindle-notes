var Alex = Vue.extend({
    data: function() {
        return { message: 'This is a test' }
    },

    template: '{{ message }}'
})

Vue.component('alex', Alex)

new Vue({
    el: '#example'
})