<?php namespace EEV\Blog;

use Backend\Models\User as Admin;
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
                return $model->name . ' - ' . $model->email;;
            });
        });

        parent::boot();
    }

    public function registerComponents()
    {
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
