<?php namespace EEV\Blog;

use Backend\Models\User as Admin;
use EEV\Blog\Components\BlogPost;
use EEV\Blog\Components\BlogPosts;
use EEV\Blog\Models\Post;
use EEV\Blog\Models\PostCategory;
use Illuminate\Support\Facades\Event;
use October\Rain\Auth\Models\User;
use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public $require = ['RainLab.User'];

    public function boot()
    {
        Event::listen('pages.menuitem.listTypes', function () {
            return [
                'blog-category' => 'Blog Category',
                'blog-post'     => 'Blog Post',
                'all-blog-categories' => 'All Categories',
            ];
        });

        Event::listen('pages.menuitem.getTypeInfo', function ($type) {
            if ($type == 'blog-category' || $type == 'all-blog-categories') {
                return PostCategory::getMenuTypeInfo($type);
            } elseif ($type == 'blog-post') {
                return Post::getMenuTypeInfo($type);
            }
        });

        Event::listen('pages.menuitem.resolveItem', function ($type, $item, $url, $theme) {
            if ($type == 'blog-category' || $type == 'all-blog-categories') {
                return PostCategory::resolveMenuItem($item, $url, $theme);
            } elseif ($type == 'blog-post') {
                return Post::resolveMenuItem($item, $url, $theme);
            }
        });

        Admin::extend(function ($model) {
            $model->addDynamicMethod('getAdminNameEmailAttribute', function () use ($model) {
                return $model->getFullNameAttribute() . ' - ' . $model->email;;
            });
        });

        User::extend(function ($model) {
            $model->addDynamicMethod('getUserNameEmailAttribute', function () use ($model) {
                return $model->name . ' - ' . $model->email;
            });
        });

        Event::listen('cms.page.beforeDisplay', function ($controller, $action, $params) {
            $controller->addCss('/plugins/eev/blog/assets/css/styles.min.css');
//            $controller->addJs('/plugins/eev/blog/assets/js/scripts.min.js');
        });

        Event::listen('backend.page.beforeDisplay', function ($controller, $action, $params) {
            $controller->addCss('/plugins/eev/blog/assets/css/admin.min.css');
        });

        parent::boot();
    }

    public function registerComponents()
    {
        return [
            BlogPosts::class => 'blogPosts',
            BlogPost::class  => 'blogPost',
        ];
    }

    public function registerSettings()
    {
        return [
            'settings' => [
                'label'       => 'Blog Settings',
                'description' => '',
                'category'    => 'Custom',
                'icon'        => 'icon-edit',
                'class'       => 'EEV\Blog\Models\Settings',
                'order'       => 500,
                'keywords'    => '',
                'permissions' => ['eev.blog.settings']
            ]
        ];
    }
}
