<?php

namespace EEV\Blog\Components;

use Backend\Facades\BackendAuth;
use Cms\Classes\ComponentBase;
use Cms\Classes\Page;
use EEV\Blog\Models\Post;
use EEV\Blog\Models\PostCategory;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use October\Rain\Database\Collection;
use October\Rain\Database\Model;

class BlogPosts extends ComponentBase
{
    /**
     * @var Collection
     */
    public $posts;

    /**
     * @var Model
     */
    public $category;

    /**
     * @var string
     */
    public $noPostsMessage;

    /**
     * Reference to the page name for linking to posts
     *
     * @var string
     */
    public $postPage;

    /**
     * Reference to the page name for linking to categories
     *
     * @var string
     */
    public $categoryPage;

    /**
     * If the post list should be ordered by another attribute
     *
     * @var string
     */
    public $sortOrder;

    public $breadcrumbs = [];

    public function componentDetails()
    {
        return [
            'name'        => 'Blog Posts',
            'description' => 'Displays a collection of blog posts.'
        ];
    }

    public function defineProperties()
    {
        return [
            'categoryFilter'   => [
                'title'       => 'eev.blog::lang.settings.posts_filter',
                'description' => 'eev.blog::lang.settings.posts_filter_description',
                'type'        => 'string',
                'default'     => '',
            ],
            'postsPerPage'     => [
                'title'             => 'eev.blog::lang.settings.posts_per_page',
                'type'              => 'string',
                'validationPattern' => '^[0-9]+$',
                'validationMessage' => 'eev.blog::lang.settings.posts_per_page_validation',
                'default'           => '10',
            ],
            'noPostsMessage'   => [
                'title'             => 'eev.blog::lang.settings.posts_no_posts',
                'description'       => 'eev.blog::lang.settings.posts_no_posts_description',
                'type'              => 'string',
                'default'           => Lang::get('eev.blog::lang.settings.posts_no_posts_default'),
                'showExternalParam' => false,
            ],
            'sortOrder'        => [
                'title'       => 'eev.blog::lang.settings.posts_order',
                'description' => 'eev.blog::lang.settings.posts_order_description',
                'type'        => 'dropdown',
                'default'     => 'published_at desc',
            ],
            'categoryPage'     => [
                'title'       => 'eev.blog::lang.settings.posts_category',
                'description' => 'eev.blog::lang.settings.posts_category_description',
                'type'        => 'dropdown',
                'default'     => 'blog/category',
                'group'       => 'eev.blog::lang.settings.group_links',
            ],
            'postPage'         => [
                'title'       => 'eev.blog::lang.settings.posts_post',
                'description' => 'eev.blog::lang.settings.posts_post_description',
                'type'        => 'dropdown',
                'default'     => 'blog/post',
                'group'       => 'eev.blog::lang.settings.group_links',
            ],
            'exceptPost'       => [
                'title'             => 'eev.blog::lang.settings.posts_except_post',
                'description'       => 'eev.blog::lang.settings.posts_except_post_description',
                'type'              => 'string',
                'validationPattern' => '^[a-z0-9\-_,\s]+$',
                'validationMessage' => 'eev.blog::lang.settings.posts_except_post_validation',
                'default'           => '',
                'group'             => 'eev.blog::lang.settings.group_exceptions',
            ],
            'exceptCategories' => [
                'title'             => 'eev.blog::lang.settings.posts_except_categories',
                'description'       => 'eev.blog::lang.settings.posts_except_categories_description',
                'type'              => 'string',
                'validationPattern' => '^[a-z0-9\-_,\s]+$',
                'validationMessage' => 'eev.blog::lang.settings.posts_except_categories_validation',
                'default'           => '',
                'group'             => 'eev.blog::lang.settings.group_exceptions',
            ],
        ];
    }

    public function getCategoryPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function getPostPageOptions()
    {
        return Page::sortBy('baseFileName')->lists('baseFileName', 'baseFileName');
    }

    public function getSortOrderOptions()
    {
        $options = Post::$allowedSortingOptions;

        foreach ($options as $key => $value) {
            $options[$key] = Lang::get($value);
        }

        return $options;
    }

    public function onRun()
    {
        $this->prepareVars();

        try {
            $this->category = $this->page['category'] = $this->loadCategory();
        } catch (ModelNotFoundException $e) {
            $this->setStatusCode(404);
            return $this->controller->run('404');
        }

        $this->posts = $this->page['posts'] = $this->listPosts();

        if ($this->category) {
            $this->page->title = $this->category->title;
            $this->breadcrumbs = $this->page['breadcrumbs'] = $this->getBreadcrumbs();
        }
    }

    protected function prepareVars()
    {
        $this->noPostsMessage = $this->page['noPostsMessage'] = $this->property('noPostsMessage');

        $this->postPage     = $this->page['postPage'] = $this->property('postPage');
        $this->categoryPage = $this->page['categoryPage'] = $this->property('categoryPage');
    }

    protected function listPosts()
    {
        $category = $this->category ? $this->category->id : null;

        $isPublished = ! $this->checkEditor();

        $posts = Post::with('categories')->listFrontEnd([
            'sort'             => $this->property('sortOrder'),
            'perPage'          => $this->property('postsPerPage'),
            'search'           => trim(input('search')),
            'category'         => $category,
            'published'        => $isPublished,
            'exceptPost'       => is_array($this->property('exceptPost'))
                ? $this->property('exceptPost')
                : preg_split('/,\s*/', $this->property('exceptPost'), -1, PREG_SPLIT_NO_EMPTY),
            'exceptCategories' => is_array($this->property('exceptCategories'))
                ? $this->property('exceptCategories')
                : preg_split('/,\s*/', $this->property('exceptCategories'), -1, PREG_SPLIT_NO_EMPTY),
        ]);

        $posts->each(function ($post) {
            $post->setUrl($this->postPage, $this->controller);

            $post->categories->each(function ($category) {
                $category->setUrl($this->categoryPage, $this->controller);
            });
        });

        return $posts;
    }

    protected function loadCategory()
    {
        if ( ! $filter = $this->property('categoryFilter')) {
            return null;
        }

        $parts = explode('/', $this->param($filter));
        $slug = array_pop($parts);

        $categoryQuery = PostCategory::where('slug', $slug);

        if ( ! $this->checkEditor()) {
            $categoryQuery->active();
        }

        $category = $categoryQuery->firstOrFail();

        $parents = $category->getParents();

        $parents->each(function ($parentCategory) {
            if (!$parentCategory->isActive() && ! $this->checkEditor()) {
                throw new ModelNotFoundException('not found');
            }
        });

        if ($parents->pluck('slug')->toArray() !== $parts) {
            throw new ModelNotFoundException('not found');
        }

        return $category ?: null;
    }

    protected function getBreadcrumbs() {

        $parents = $this->category->getParents();

        return $parents->reduce(function ($result, $parentCategory) {
            $parentCategory->setUrl($this->categoryPage, $this->controller);

            $result[] = [
                'label' => $parentCategory->title,
                'url' => $parentCategory->url,
            ];

            return $result;
        });
    }

    protected function checkEditor()
    {
        $backendUser = BackendAuth::getUser();

        return $backendUser && $backendUser->hasAccess('eev.blog.posts') && Config::get('eev.blog::show_all_posts',
                true);
    }

}