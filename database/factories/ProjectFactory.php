<?php

use App\User;
use App\Project;
use Illuminate\Support\Str;
use Faker\Generator as Faker;

$factory->define(Project::class, function (Faker $faker) {
    return [
        'name' => $faker->sentence($nbWords = 2, $variableNbWords = true),
        'project_hash' => Str::random(40),
        'company' => $faker->company,
        'status' => 'draft',
        'type' => 'design',
        'created_by' => function () {
            return factory(User::class)->create()->id;
        }
    ];
});
