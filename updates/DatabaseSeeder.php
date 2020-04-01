<?php

namespace EEV\Blog\Updates;

use EEV\Blog\Models\Post;
use October\Rain\Database\Updates\Seeder;
use Faker;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker\Factory::create('ru_RU');

        Post::insert([
            [
                'title' => $faker->realText(50),
                'content' => $faker->realText(2000),
                'preview' => $faker->realText(500),
                'slug' => 'post-1',
            ],
            [
                'title' => $faker->realText(50),
                'content' => $faker->realText(2000),
                'preview' => $faker->realText(500),
                'slug' => 'post-2',
            ],
        ]);
    }
}