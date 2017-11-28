<?php

$factory->define(App\Tag::class, function (Faker\Generator $faker) {
    $title = $faker->word;
    return [
        'user_id' => function() {
          return factory('App\User')->create()->id;
        },
        'tag' => $title,
        'slug' => $title
    ];
});
