<?php

use Faker\Generator as Faker;
use Illuminate\Support\Str;


/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\User::class, function (Faker $faker) {
    return [
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'email' => $faker->unique()->safeEmail,
        'password' => '$2y$10$TKh8H1.PfQx37YgCzwiKb.KjNyWgaHb9cbcoQgdIVFlYg7B77UdFm', // secret
        'remember_token' => Str::random(10),
        'role' => 'user',
        'active' => true,
    ];
});

$factory->afterCreating(App\User::class, function ($user, $faker) {
    $user->profile()->save(factory(\App\UserProfile::class)->make());
});

$factory->state(\App\User::class, 'inactive', function (Faker $faker) {
    return [
        'active' => false,
    ];
});