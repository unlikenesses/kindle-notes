<?php

$factory->define(App\Tag::class, function (Faker\Generator $faker) {
    $title = $faker->word;
    return [
        'tag' => $title,
        'slug' => $title
    ];
});