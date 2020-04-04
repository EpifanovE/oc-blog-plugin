<?php namespace EEV\Blog\Models;

use Cms\Classes\Page as CmsPage;
use Cms\Classes\Theme;
use EEV\Blog\Classes\Status;
use Illuminate\Support\Facades\URL;
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
        'title'           => [
            'required',
            'unique:eev_blog_posts,title',
            'max:1024',
        ],
        'content'         => [
            'string',
            'nullable',
            'max:65535',
        ],
        'preview'         => [
            'string',
            'nullable',
            'max:1024',
        ],
        'image'           => [
            'image',
            'mimes:jpeg,png,gif,webp'
        ],
        'status'          => [
            'in:published,draft,disabled'
        ],
        'slug'            => [
            'string',
            'nullable',
            'max:1024',
        ],
        'seo_title'       => [
            'string',
            'nullable',
            'max:1024',
        ],
        'seo_description' => [
            'string',
            'nullable',
            'max:2048',
        ],
        'seo_keywords'    => [
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

    public static function getMenuTypeInfo($type)
    {
        $result = [];

        if ($type == 'blog-category') {
            $result = [
                'references'   => self::listSubCategoryOptions(),
                'nesting'      => true,
                'dynamicItems' => true
            ];
        }

        if ($type == 'all-blog-categories') {
            $result = [
                'nesting'      => true,
                'dynamicItems' => true
            ];
        }

        if ($result) {
            $theme = Theme::getActiveTheme();

            $pages    = CmsPage::listInTheme($theme, true);
            $cmsPages = [];
            foreach ($pages as $page) {
                if ( ! $page->hasComponent('blogPosts')) {
                    continue;
                }

                /*
                 * Component must use a category filter with a routing parameter
                 * eg: categoryFilter = "{{ :somevalue }}"
                 */
                $properties = $page->getComponentProperties('blogPosts');

                if ( ! isset($properties['categoryFilter'])) {
                    continue;
                }

                $cmsPages[] = $page;
            }

            $result['cmsPages'] = $cmsPages;
        }

        return $result;
    }

    protected static function listSubCategoryOptions()
    {
        $category = self::getNested();

        $iterator = function ($categories) use (&$iterator) {
            $result = [];

            foreach ($categories as $category) {
                if ( ! $category->children) {
                    $result[$category->id] = $category->name;
                } else {
                    $result[$category->id] = [
                        'title' => $category->title,
                        'items' => $iterator($category->children)
                    ];
                }
            }

            return $result;
        };

        return $iterator($category);
    }

    public static function resolveMenuItem($item, $url, $theme)
    {
        $result = null;

        if ($item->type == 'blog-category') {
            if ( ! $item->reference || ! $item->cmsPage) {
                return;
            }

            $category = self::find($item->reference);
            if ( ! $category) {
                return;
            }

            $pageUrl = self::getCategoryPageUrl($item->cmsPage, $category, $theme);
            if ( ! $pageUrl) {
                return;
            }

            $pageUrl = Url::to($pageUrl);

            $result             = [];
            $result['url']      = $pageUrl;
            $result['isActive'] = $pageUrl == $url;
            $result['mtime']    = $category->updated_at;

            if ($item->nesting) {
                $categories = $category->getChildren();
                $iterator   = function ($categories) use (&$iterator, &$item, &$theme, $url) {
                    $branch = [];

                    foreach ($categories as $category) {

                        $branchItem             = [];
                        $branchItem['url']      = self::getCategoryPageUrl($item->cmsPage, $category, $theme);
                        $branchItem['isActive'] = $branchItem['url'] == $url;
                        $branchItem['title']    = $category->title;
                        $branchItem['mtime']    = $category->updated_at;

                        if ($category->children) {
                            $branchItem['items'] = $iterator($category->children);
                        }

                        $branch[] = $branchItem;
                    }

                    return $branch;
                };

                $result['items'] = $iterator($categories);
            }
        } elseif ($item->type == 'all-blog-categories') {
            $result = [
                'items' => []
            ];

            $categories = self::active()->orderBy('title')->getAllRoot();

            if ($item->nesting) {
                $iterator   = function ($categories) use (&$iterator, &$item, &$theme, $url) {
                    $branch = [];

                    foreach ($categories as $category) {

                        $branchItem             = [];
                        $branchItem['url']      = self::getCategoryPageUrl($item->cmsPage, $category, $theme);
                        $branchItem['isActive'] = $branchItem['url'] == $url;
                        $branchItem['title']    = $category->title;
                        $branchItem['mtime']    = $category->updated_at;

                        if ($category->children) {
                            $branchItem['items'] = $iterator($category->children);
                        }

                        $branch[] = $branchItem;
                    }

                    return $branch;
                };

                $result['items'] = $iterator($categories);
            } else {
                foreach ($categories as $category) {
                    $categoryItem = [
                        'title' => $category->title,
                        'url'   => self::getCategoryPageUrl($item->cmsPage, $category, $theme),
                        'mtime' => $category->updated_at
                    ];

                    $categoryItem['isActive'] = $categoryItem['url'] == $url;

                    $result['items'][] = $categoryItem;
                }
            }
        }

        return $result;
    }

    public function isActive()
    {
        return $this->status === Status::PUBLISHED;
    }

    public function setUrl($pageName, $controller)
    {
        $params = [
            'path' => $this->getUrl(),
        ];

        return $this->url = $controller->pageUrl($pageName, $params, false);
    }

    public function getStatusLabelAttribute()
    {
        return [
            'label'    => Lang::get('eev.blog::lang.statuses.' . $this->status),
            'modifier' => $this->status,
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('status', Status::PUBLISHED);
    }

    protected static function getCategoryPageUrl($pageCode, $category, $theme)
    {
        $page = CmsPage::loadCached($theme, $pageCode);
        if ( ! $page) {
            return;
        }

        $properties = $page->getComponentProperties('blogPosts');
        if ( ! isset($properties['categoryFilter'])) {
            return;
        }

        $paramName = $properties['categoryFilter'];
        $url       = CmsPage::url($page->getBaseFileName(), [$paramName => self::getCategoryUrl($category)]);

        return $url;
    }

    protected function getUrl()
    {
        return self::getCategoryUrl($this);
    }

    protected static function getCategoryUrl($category) {
        $parts   = $category->getParents()->pluck('slug')->toArray();
        $parts[] = $category->slug;

        return implode('/', $parts);
    }
}
