<?php

$factory->define(App\Book::class, function (Faker\Generator $faker) {
    return [
        'user_id' => function() {
          return factory('App\User')->create()->id;
        },
        'title' => $faker->sentence,
        'author_first_name' => $faker->firstName,
        'author_last_name' => $faker->lastName
    ];
});
