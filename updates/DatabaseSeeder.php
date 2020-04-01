<?php

namespace EEV\Blog\Updates;

use EEV\Blog\Models\Post;
use EEV\Blog\Models\PostCategory;
use October\Rain\Database\Updates\Seeder;
use Faker;

class DatabaseSeeder extends Seeder
{
    private $faker;

    public function __construct()
    {
        $this->faker = Faker\Factory::create('ru_RU');
    }

    public function run()
    {
        for($i = 1; $i < 100; $i++) {
            Post::create($this->getPost($i));
        }

        for($i = 1; $i < 10; $i++) {
            PostCategory::create($this->getPostCategory($i));
        }
    }

    public function getPost($index = null) {
        return [
            'title' => $this->faker->realText(50),
            'content' => $this->faker->realText(2000),
            'preview' => $this->faker->realText(500),
            'slug' => 'post-' . ($index ? $index : ''),
        ];
    }

    public function getPostCategory($index = null) {
        return [
            'title' => $this->faker->realText(20),
            'content' => $this->faker->realText(2000),
            'preview' => $this->faker->realText(500),
            'slug' => 'category-' . ($index ? $index : ''),
        ];
    }
}