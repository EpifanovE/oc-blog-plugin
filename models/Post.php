<?php namespace EEV\Blog\Models;

use Backend\Models\User;
use BackendAuth;
use Cms\Classes\Controller;
use EEV\Blog\Classes\Status;
use Lang;
use Model;
use October\Rain\Database\Relations\BelongsToMany;
use October\Rain\Database\Traits\Sluggable;
use October\Rain\Database\Traits\SoftDelete;
use October\Rain\Database\Traits\Validation;

/**
 * Model
 *
 * @method BelongsToMany categories
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
            'max:1024',
        ],
        'image' => [
            'string',
        ],
        'status' => [
            'in:published,draft,disabled'
        ],
        'slug' => [
            'string',
            'nullable',
            'max:1024',
            'regex:/^[a-z0-9\/\:_\-\*\[\]\+\?\|]*$/i', 'unique:eev_blog_posts',
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
            'key'      => 'post_id',
            'otherKey' => 'id',
        ],
    ];

    public $belongsTo = [
        'author' => [
            User::class,
            'key' => 'user_id',
            'otherKey' => 'id',
        ],
    ];

    /**
     * The attributes on which the post list can be ordered.
     * @var array
     */
    public static $allowedSortingOptions = [
        'title asc'         => 'rainlab.blog::lang.sorting.title_asc',
        'title desc'        => 'rainlab.blog::lang.sorting.title_desc',
        'created_at asc'    => 'rainlab.blog::lang.sorting.created_asc',
        'created_at desc'   => 'rainlab.blog::lang.sorting.created_desc',
        'updated_at asc'    => 'rainlab.blog::lang.sorting.updated_asc',
        'updated_at desc'   => 'rainlab.blog::lang.sorting.updated_desc',
        'published_at asc'  => 'rainlab.blog::lang.sorting.published_asc',
//        'published_at desc' => 'rainlab.blog::lang.sorting.published_desc',
        'random'            => 'rainlab.blog::lang.sorting.random'
    ];

    public function isActive() {
        return $this->status === Status::PUBLISHED;
    }

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

    public function scopeListFrontEnd($query, $options)
    {
        /*
         * Default options
         */
        extract(array_merge([
            'page'             => 1,
            'perPage'          => 30,
            'sort'             => 'created_at',
            'categories'       => null,
            'exceptCategories' => null,
            'category'         => null,
            'search'           => '',
            'published'        => true,
            'exceptPost'       => null
        ], $options));

        $searchableFields = ['title', 'slug', 'excerpt', 'content'];

        if ($published) {
            $query->isActive();
        }

        /*
         * Except post(s)
         */
        if ($exceptPost) {
            $exceptPosts = (is_array($exceptPost)) ? $exceptPost : [$exceptPost];
            $exceptPostIds = [];
            $exceptPostSlugs = [];

            foreach ($exceptPosts as $exceptPost) {
                $exceptPost = trim($exceptPost);

                if (is_numeric($exceptPost)) {
                    $exceptPostIds[] = $exceptPost;
                } else {
                    $exceptPostSlugs[] = $exceptPost;
                }
            }

            if (count($exceptPostIds)) {
                $query->whereNotIn('id', $exceptPostIds);
            }
            if (count($exceptPostSlugs)) {
                $query->whereNotIn('slug', $exceptPostSlugs);
            }
        }

        /*
         * Sorting
         */
        if (in_array($sort, array_keys(static::$allowedSortingOptions))) {
            if ($sort == 'random') {
                $query->inRandomOrder();
            } else {
                @list($sortField, $sortDirection) = explode(' ', $sort);

                if (is_null($sortDirection)) {
                    $sortDirection = "desc";
                }

                $query->orderBy($sortField, $sortDirection);
            }
        }

        /*
         * Search
         */
        $search = trim($search);
        if (strlen($search)) {
            $query->searchWhere($search, $searchableFields);
        }

        /*
         * Categories
         */
        if ($categories !== null) {
            $categories = is_array($categories) ? $categories : [$categories];
            $query->whereHas('categories', function($q) use ($categories) {
                $q->whereIn('id', $categories);
            });
        }

        /*
         * Except Categories
         */
        if (!empty($exceptCategories)) {
            $exceptCategories = is_array($exceptCategories) ? $exceptCategories : [$exceptCategories];
            array_walk($exceptCategories, 'trim');

            $query->whereDoesntHave('categories', function ($q) use ($exceptCategories) {
                $q->whereIn('slug', $exceptCategories);
            });
        }

        /*
         * Category, including children
         */
        if ($category !== null) {
            $category = PostCategory::find($category);

            $categories = $category->getAllChildrenAndSelf()->lists('id');
            $query->whereHas('categories', function($q) use ($categories) {
                $q->whereIn('id', $categories);
            });
        }

        return $query->paginate($perPage, $page);
    }

    public function scopeActive($query) {
        return $query->where('status', Status::PUBLISHED);
    }

    public function beforeSave()
    {
        if (empty($this->author)) {
            $user = BackendAuth::getUser();
            if (!is_null($user)) {
                $this->user_id = $user->id;
            }
        }
    }

    public function getStatusLabelAttribute()
    {
        return [
            'label' => Lang::get('eev.blog::lang.statuses.' . $this->status),
            'modifier' => $this->status,
        ];
    }
}
