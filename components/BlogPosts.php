<?php

namespace EEV\Blog\Components;

use Cms\Classes\ComponentBase;
use EEV\Blog\Models\Post;

class BlogPosts extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name' => 'Blog Posts',
            'description' => 'Displays a collection of blog posts.'
        ];
    }

    public function posts()
    {
        return Post::all();
    }

    public function defineProperties()
    {
        return [
            'title' => [
                'title'             => 'eev.blog::lang.title',
                'description'       => '',
                'default'           => '',
                'type'              => 'string',
            ],
            'subtitle' => [
                'title'             => 'eev.blog::lang.subtitle',
                'description'       => '',
                'default'           => '',
                'type'              => 'string',
            ],
        ];
    }

}