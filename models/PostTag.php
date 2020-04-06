<?php namespace EEV\Blog\Models;

use Model;
use October\Rain\Database\Traits\Sluggable;
use October\Rain\Database\Traits\SoftDelete;
use October\Rain\Database\Traits\Sortable;
use October\Rain\Database\Traits\Validation;

class PostTag extends Model
{
    use Validation, SoftDelete, Sluggable, Sortable;

    const SORT_ORDER = 'sort';

    protected $dates = ['deleted_at'];

    protected $slugs = ['slug' => 'title'];

    public $table = 'eev_blog_post_tag';

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
            'table'    => 'eev_blog_post_post_tag',
            'key'      => 'post_tag_id',
            'otherKey' => 'post_id',
        ]
    ];

    public function setUrl($pageName, $controller)
    {
        $params = [
            'slug' => $this->slug,
        ];

        return $this->url = $controller->pageUrl($pageName, $params, false);
    }
}
