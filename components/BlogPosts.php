<?php

namespace EEV\Blog\Components;

use Backend\Facades\BackendAuth;
use Cms\Classes\ComponentBase;
use Cms\Classes\Page;
use EEV\Blog\Models\Post;
use EEV\Blog\Models\PostCategory;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Lang;
use October\Rain\Database\Collection;
use October\Rain\Database\Model;

class BlogPosts extends ComponentBase
{
    /**
     * A collection of posts to display
     *
     * @var Collection
     */
    public $posts;

    /**
     * Parameter to use for the page number
     *
     * @var string
     */
    public $pageParam;

    /**
     * If the post list should be filtered by a category, the model to use
     *
     * @var Model
     */
    public $category;

    /**
     * Message to display when there are no messages
     *
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

    public function componentDetails()
    {
        return [
            'name' => 'Blog Posts',
            'description' => 'Displays a collection of blog posts.'
        ];
    }

    public function posts()
    {
        return Post::active()->paginate(10);
    }

    public function defineProperties()
    {
        return [
            'pageNumber' => [
                'title'       => 'eev.blog::lang.settings.posts_pagination',
                'description' => 'eev.blog::lang.settings.posts_pagination_description',
                'type'        => 'string',
                'default'     => '{{ :page }}',
            ],
            'categoryFilter' => [
                'title'       => 'eev.blog::lang.settings.posts_filter',
                'description' => 'eev.blog::lang.settings.posts_filter_description',
                'type'        => 'string',
                'default'     => '',
            ],
            'postsPerPage' => [
                'title'             => 'eev.blog::lang.settings.posts_per_page',
                'type'              => 'string',
                'validationPattern' => '^[0-9]+$',
                'validationMessage' => 'eev.blog::lang.settings.posts_per_page_validation',
                'default'           => '10',
            ],
            'noPostsMessage' => [
                'title'             => 'eev.blog::lang.settings.posts_no_posts',
                'description'       => 'eev.blog::lang.settings.posts_no_posts_description',
                'type'              => 'string',
                'default'           => Lang::get('eev.blog::lang.settings.posts_no_posts_default'),
                'showExternalParam' => false,
            ],
            'sortOrder' => [
                'title'       => 'eev.blog::lang.settings.posts_order',
                'description' => 'eev.blog::lang.settings.posts_order_description',
                'type'        => 'dropdown',
                'default'     => 'published_at desc',
            ],
            'categoryPage' => [
                'title'       => 'eev.blog::lang.settings.posts_category',
                'description' => 'eev.blog::lang.settings.posts_category_description',
                'type'        => 'dropdown',
                'default'     => 'blog/category',
                'group'       => 'eev.blog::lang.settings.group_links',
            ],
            'postPage' => [
                'title'       => 'eev.blog::lang.settings.posts_post',
                'description' => 'eev.blog::lang.settings.posts_post_description',
                'type'        => 'dropdown',
                'default'     => 'blog/post',
                'group'       => 'eev.blog::lang.settings.group_links',
            ],
            'exceptPost' => [
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

    public function onRun()
    {
        $this->prepareVars();

        $this->category = $this->page['category'] = $this->loadCategory();
        $this->posts = $this->page['posts'] = $this->listPosts();

        /*
         * If the page number is not valid, redirect
         */
        if ($pageNumberParam = $this->paramName('pageNumber')) {
            $currentPage = $this->property('pageNumber');

            if ($currentPage > ($lastPage = $this->posts->lastPage()) && $currentPage > 1) {
                return Redirect::to($this->currentPageUrl([$pageNumberParam => $lastPage]));
            }
        }
    }

    protected function prepareVars()
    {
        $this->pageParam = $this->page['pageParam'] = $this->paramName('pageNumber');
        $this->noPostsMessage = $this->page['noPostsMessage'] = $this->property('noPostsMessage');

        /*
         * Page links
         */
        $this->postPage = $this->page['postPage'] = $this->property('postPage');
        $this->categoryPage = $this->page['categoryPage'] = $this->property('categoryPage');
    }

    protected function listPosts()
    {
        $category = $this->category ? $this->category->id : null;

        /*
         * List all the posts, eager load their categories
         */
        $isPublished = !$this->checkEditor();

        $posts = Post::with('categories')->listFrontEnd([
            'page'             => $this->property('pageNumber'),
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

        /*
         * Add a "url" helper attribute for linking to each post and category
         */
        $posts->each(function($post) {
            $post->setUrl($this->postPage, $this->controller);

            $post->categories->each(function($category) {
                $category->setUrl($this->categoryPage, $this->controller);
            });
        });

        return $posts;
    }

    protected function loadCategory()
    {
        if (!$slug = $this->property('categoryFilter')) {
            return null;
        }

        $category = PostCategory::where('slug', $slug)->first();

        return $category ?: null;
    }

    protected function checkEditor()
    {
        $backendUser = BackendAuth::getUser();

        return $backendUser && $backendUser->hasAccess('eev.blog.posts') && Config::get('eev.blog::show_all_posts', true);
    }

}