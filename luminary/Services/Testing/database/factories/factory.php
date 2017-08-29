<?php

use Luminary\Services\Testing\Models\Customer;
use Luminary\Services\Testing\Models\Interest;
use Luminary\Services\Testing\Models\Location;
use Luminary\Services\Testing\Models\User;

$tenant_id = function () {
    static $first;
    static $second;

    // make sure we have at least one
    if (is_null($first)) {
        $first = 1234;
        return 1234;
    }

    // make sure we have at least one
    if (is_null($second)) {
        $second = 2345;
        return 2345;
    }

    return rand(0, 1) ? 1234 : 2345;
};

$factory->define(User::class, function (Faker\Generator $faker) use ($tenant_id) {
    return [
        'first_name' => $faker->firstName,
        'last_name' => $faker->lastName,
        'email' => $faker->unique()->safeEmail,
        'tenant_id' => $tenant_id()
    ];
});

$factory->define(Customer::class, function (Faker\Generator $faker) use ($tenant_id) {
    return [
        'name' => $faker->company,
        'website' => $faker->domainName,
        'phone' => $faker->phoneNumber,
        'tenant_id' => $tenant_id()
    ];
});

$factory->define(Location::class, function (Faker\Generator $faker) use ($tenant_id) {
    return [
        'name' => $faker->company,
        'street' => $faker->streetAddress,
        'city' => $faker->city,
        'zip' => $faker->postcode,
        'phone' => $faker->phoneNumber,
        'tenant_id' => $tenant_id()
    ];
});

$factory->define(Interest::class, function (Faker\Generator $faker) use ($tenant_id) {
    return [
        'name' => $faker->safeColorName
    ];
});
