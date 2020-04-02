<?php

namespace EEV\Blog\Components;

use Cms\Classes\ComponentBase;
use EEV\Blog\Models\Post;
use Cms\Classes\Page;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BlogPost extends ComponentBase
{
    public function componentDetails()
    {
        return [
            'name'        => 'Blog Post',
            'description' => 'Displays a blog post.'
        ];
    }

    public function defineProperties()
    {
        return [
            'slug'         => [
                'title'       => 'eev.blog::lang.post_slug',
                'description' => 'eev.blog::lang.post_slug_description',
                'default'     => '{{ :slug }}',
                'type'        => 'string',
            ],
            'categoryPage' => [
                'title'       => 'eev.blog::lang.post_category',
                'description' => 'eev.blog::lang.post_category_description',
                'type'        => 'dropdown',
                'default'     => 'blog/category',
            ],
        ];
    }

    public function getCategoryPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function onRun()
    {
        $slug = $this->property('slug');

        $post = Post::where('slug', $slug)->active()->first();

        if ( ! $post) {
            $this->setStatusCode(404);

            return $this->controller->run('404');
        }

        $this->page['post'] = $post;
    }

    protected function loadPost()
    {
        $slug = $this->property('slug');

        try {
            $post = Post::where('slug', $slug)->active()->firstOrFail();
        } catch (ModelNotFoundException $ex) {
            $this->setStatusCode(404);
            return $this->controller->run('404');
        }


        if ( ! $this->checkEditor()) {
            $post = $post->isPublished();
        }

        try {
            $post = $post->firstOrFail();
        } catch (ModelNotFoundException $ex) {
            $this->setStatusCode(404);

            return $this->controller->run('404');
        }

        /*
         * Add a "url" helper attribute for linking to each category
         */
        if ($post && $post->categories->count()) {
            $post->categories->each(function ($category) {
                $category->setUrl($this->categoryPage, $this->controller);
            });
        }

        return $post;
    }

    protected function checkEditor()
    {
        $backendUser = BackendAuth::getUser();

        return $backendUser && $backendUser->hasAccess('eev.blog.posts');
    }

}