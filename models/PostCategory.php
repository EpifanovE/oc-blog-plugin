<?php namespace EEV\Blog\Models;

use Lang;
use Model;
use October\Rain\Database\Traits\NestedTree;
use October\Rain\Database\Traits\Sluggable;
use October\Rain\Database\Traits\SoftDelete;
use October\Rain\Database\Traits\Validation;

class PostCategory extends Model
{
    use Validation, SoftDelete, NestedTree, Sluggable;

    protected $dates = ['deleted_at'];

    public $table = 'eev_blog_post_categories';

    protected $slugs = ['slug' => 'title'];

    public $rules = [
        'title' => [
            'required',
            'unique:eev_blog_posts,title',
            'max:1024',
        ],
        'content' => [
            'string',
            'nullable',
            'max:65535',
        ],
        'preview' => [
            'string',
            'nullable',
            'max:1024',
        ],
        'image' => [
            'image',
            'mimes:jpeg,png,gif,webp'
        ],
        'status' => [
            'in:published,draft,disabled'
        ],
        'slug' => [
            'string',
            'nullable',
            'max:1024',
        ],
        'seo_title' => [
            'string',
            'nullable',
            'max:1024',
        ],
        'seo_description' => [
            'string',
            'nullable',
            'max:2048',
        ],
        'seo_keywords' => [
            'string',
            'nullable',
            'max:1024',
        ],
    ];

    public $belongsToMany = [
        'posts' => [
            Post::class,
            'table'    => 'eev_blog_post_post_category',
            'key'      => 'post_category_id',
            'otherKey' => 'post_id',
        ]
    ];

    public function getStatusLabelAttribute()
    {
        return [
            'label' => Lang::get('eev.blog::lang.statuses.' . $this->status),
            'modifier' => $this->status,
        ];
    }
}
