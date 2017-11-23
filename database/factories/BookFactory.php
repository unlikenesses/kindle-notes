<?php

$factory->define(App\Book::class, function (Faker\Generator $faker) {
    $title = $faker->sentence;
    return [
        'user_id' => function() {
          return factory('App\User')->create()->id;
        },
        'title_string' => $title,
        'title' => $title,
        'author_first_name' => $faker->firstName,
        'author_last_name' => $faker->lastName
    ];
});
