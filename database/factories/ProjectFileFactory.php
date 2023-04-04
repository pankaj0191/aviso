<?php

use App\User;
use App\ProjectFile;
use Faker\Generator as Faker;

$factory->define(ProjectFile::class, function (Faker $faker) {
    return [
        'path' => 'fake-test-path',
        'thumb_path' => 'fake-test-thumb-path',
        'revision_id' => 0,
        'file_type' => 'picture',
        'owner_type' => 'proof',
        'user_id' => function () {
            return factory(User::class)->create();
        },
    ];
});
