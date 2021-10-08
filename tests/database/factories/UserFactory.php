<?php

use Faker\Generator;
use \Illuminate\Support\Str;
use Itsjeffro\Panel\Tests\Models\User;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(User::class, function (Generator $faker) {
    return [
        'name' => $faker->name,
        'email' => $faker->unique()->safeEmail,
        'email_verified_at' => now(),
        'password' => bcrypt('demo123'),
        'remember_token' => Str::random(10),
    ];
});
