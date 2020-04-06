<?php

namespace EEV\Blog\Components;

use Backend\Facades\BackendAuth;
use Cms\Classes\ComponentBase;
use EEV\Blog\Models\Post;
use Cms\Classes\Page;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class BlogPost extends ComponentBase
{

    public $post;

    public $categoryPage;

    public $tagPage;

    public function componentDetails()
    {
        return [
            'name' => 'Blog Post',
            'description' => 'Displays a blog post.'
        ];
    }

    public function defineProperties()
    {
        return [
            'slug' => [
                'title' => 'eev.blog::lang.post_slug',
                'description' => 'eev.blog::lang.post_slug_description',
                'default' => '{{ :slug }}',
                'type' => 'string',
            ],
            'categoryPage' => [
                'title' => 'eev.blog::lang.category_page',
                'description' => '',
                'type' => 'dropdown',
                'default' => 'blog/category',
            ],
            'tagPage' => [
                'title' => 'eev.blog::lang.tag_page',
                'description' => '',
                'type' => 'dropdown',
                'default' => 'blog/category',
            ],
        ];
    }

    public function getCategoryPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function getTagPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function onRun()
    {
        $this->categoryPage = $this->page['categoryPage'] = $this->property('categoryPage');
        $this->tagPage = $this->page['tagPage'] = $this->property('tagPage');

        $config = config('eev.blog::post');

        $this->page['config'] = $config;

        try {
            $this->post = $this->page['post'] = $this->loadPost();
        } catch (ModelNotFoundException $e) {
            $this->setStatusCode(404);
            return $this->controller->run('404');
        }
    }

    protected function loadPost()
    {
        $slug = $this->property('slug');

        try {
            $postQuery = Post::where('slug', $slug);

            if (!$this->checkEditor()) {
                $postQuery
                    ->active()
                    ->with(['categories' => function ($q) {
                        $q->active();
                    }]);
            }

            $post = $postQuery->firstOrFail();
        } catch (ModelNotFoundException $e) {
            throw new ModelNotFoundException('Not Found');
        }

        if ($post && $post->categories->count()) {
            $post->categories->each(function ($category) {
                $category->setUrl($this->categoryPage, $this->controller);
            });
        }

        if ($post && $post->tags->count()) {
            $post->tags->each(function ($tag) {
                $tag->setUrl($this->tagPage, $this->controller);
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