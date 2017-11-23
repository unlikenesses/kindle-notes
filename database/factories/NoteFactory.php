<?php

$factory->define(App\Note::class, function (Faker\Generator $faker) {
    $title = $faker->sentence;
    return [
        'user_id' => function() {
          return factory('App\User')->create()->id;
        },
        'book_id' => function() {
          return factory('App\Book')->create()->id; 
        },
        'page' => '12-13',
        'location' => '4000-4001',
        'date' => $faker->datetime,
        'note' => $faker->sentence,
        'type' => 1 // 1 or 2
    ];
});
