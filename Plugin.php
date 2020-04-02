<?php namespace EEV\Blog;

use Backend\Models\User as Admin;
use EEV\Blog\Components\BlogPosts;
use Event;
use October\Rain\Auth\Models\User;
use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public function boot()
    {
        Admin::extend(function($model) {
            $model->addDynamicMethod('getAdminNameEmailAttribute', function() use ($model) {
                return $model->getFullNameAttribute() . ' - ' . $model->email;;
            });
        });

        User::extend(function($model) {
            $model->addDynamicMethod('getUserNameEmailAttribute', function() use ($model) {
                return $model->name . ' - ' . $model->email;
            });
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
