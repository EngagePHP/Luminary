<?php

use Luminary\Services\Testing\Models\Customer;
use Luminary\Services\Testing\Models\Location;
use Luminary\Services\Testing\Models\User;

$factory->define(User::class, function (Faker\Generator $faker) {
    return [
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'email' => $faker->unique()->safeEmail,
    ];
});

$factory->define(Customer::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->company,
        'website' => $faker->domainName,
        'phone' => $faker->phoneNumber,
    ];
});

$factory->define(Location::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->company,
        'street' => $faker->streetAddress,
        'city' => $faker->city,
        'zip' => $faker->postcode,
        'phone' => $faker->phoneNumber,
    ];
});
