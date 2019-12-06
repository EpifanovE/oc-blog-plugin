<?php namespace EEV\Blog\Models;

use Model;
use October\Rain\Database\Traits\Sluggable;
use October\Rain\Database\Traits\SoftDelete;
use October\Rain\Database\Traits\Validation;

/**
 * Model
 */
class Post extends Model
{
    use Validation, SoftDelete, Sluggable;

    protected $dates = ['deleted_at'];

    protected $guarded = ['created_at', 'updated_at'];

    protected $slugs = ['slug' => 'title'];

    public $table = 'eev_blog_posts';

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
            'max:65535',
        ],
        'image' => [
            'image',
            'mimes:jpeg,png,gif,webp'
        ],
        'status' => [
            'in:active,draft,disabled'
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
        'categories' => [
            'array',
        ],
        'categories.*' => [
            'integer',
        ],
        'tags' => [
            'array',
        ],
        'tags.*' => [
            'integer',
        ],
    ];

    public $belongsToMany = [
        'categories' => [
            PostCategory::class,
            'table'    => 'eev_blog_post_post_category',
            'key'      => 'post_id',
            'otherKey' => 'post_category_id',
        ],
        'tags'       => [
            PostTag::class,
            'table'    => 'eev_blog_post_post_tag',
            'key'      => 'post_id',
            'otherKey' => 'post_tag_id',
        ]
    ];

    public $hasMany = [
        'comments' => [
            Comment::class,
            'key'      => 'id',
            'otherKey' => 'post_id',
        ],
    ];

    public function scopeFilterByCategory($query, $value) {
        return $query->whereHas('categories', function ($query) use ($value) {
            $query->where('id', '=', $value);
        });
    }

    public function scopeFilterByTag($query, $value) {
        return $query->whereHas('tags', function ($query) use ($value) {
            $query->where('id', '=', $value);
        });
    }
}
