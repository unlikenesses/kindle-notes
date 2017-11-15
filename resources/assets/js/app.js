
/*
 |--------------------------------------------------------------------------
 | Laravel Spark Bootstrap
 |--------------------------------------------------------------------------
 |
 | First, we will load all of the "core" dependencies for Spark which are
 | libraries such as Vue and jQuery. This also loads the Spark helpers
 | for things such as HTTP calls, forms, and form validation errors.
 |
 | Next, we'll create the root Vue application for Spark. This will start
 | the entire application and attach it to the DOM. Of course, you may
 | customize this script as you desire and load your own components.
 |
 */

require('spark-bootstrap');

require('./components/bootstrap');

import Book from './components/book/Book.vue';
import BookDetails from './components/book/BookDetails.vue';
import BookEdit from './components/book/BookEdit.vue';
import Tags from './components/book/Tags.vue';

Vue.component('book', Book);
Vue.component('bookDetails', BookDetails);
Vue.component('bookEdit', BookEdit);
Vue.component('tags', Tags);

var app = new Vue({
    mixins: [require('spark')],
});