<?php

namespace EEV\Blog\Updates;

use EEV\Blog\Classes\Status;
use EEV\Blog\Models\Post;
use EEV\Blog\Models\PostCategory;
use EEV\Blog\Models\PostTag;
use October\Rain\Database\Updates\Seeder;
use Faker;
use System\Models\File;

class DatabaseSeeder extends Seeder
{
    private $faker;

    public function __construct()
    {
        $this->faker = Faker\Factory::create('ru_RU');
    }

    public function run()
    {
        for($i = 1; $i < 10; $i++) {
            PostCategory::create($this->getPostCategory($i));
        }

        for($i = 1; $i < 100; $i++) {

            $categoriesCount = random_int(0,5);

            $post = Post::create($this->getPost($i));
            $post->categories()->attach($this->uniqueRandomNumbersWithinRange(1,10, $categoriesCount));
        }

        for($i = 1; $i < 10; $i++) {
            PostTag::create($this->getPostTag($i));
        }
    }

    public function getPost($index = null) {
        return [
            'title' => $this->faker->realText(50),
            'content' => $this->faker->realText(2000),
            'preview' => $this->faker->realText(500),
            'slug' => 'post-' . ($index ? $index : ''),
            'status' => $this->faker->randomElement([
                Status::DRAFT,
                Status::PUBLISHED,
                Status::DISABLED,
            ]),
        ];
    }

    public function getPostCategory($index = null) {
        return [
            'title' => $this->faker->realText(20),
            'content' => $this->faker->realText(2000),
            'preview' => $this->faker->realText(500),
            'slug' => 'category-' . ($index ? $index : ''),
            'status' => $this->faker->randomElement([
                Status::DRAFT,
                Status::PUBLISHED,
                Status::DISABLED,
            ]),
        ];
    }

    public function getPostTag($index = null) {
        return [
            'title' => $this->faker->realText(10),
            'content' => $this->faker->realText(2000),
            'slug' => 'tag-' . ($index ? $index : ''),
        ];
    }

    public function uniqueRandomNumbersWithinRange($min, $max, $quantity) {
        $numbers = range($min, $max);
        shuffle($numbers);
        return array_slice($numbers, 0, $quantity);
    }
}