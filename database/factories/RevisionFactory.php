<?php

use Faker\Generator as Faker;
use App\Revision;
use App\Project;

$factory->define(Revision::class, function (Faker $faker) {
    return [
        'project_id' => function() {
            return factory(Project::class)->create();
        },
        'status_revision' => 'draft',
        'version' => 1,
    ];
});
