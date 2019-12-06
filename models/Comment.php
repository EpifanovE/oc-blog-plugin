<?php namespace EEV\Blog\Models;

use Model;
use October\Rain\Auth\Models\User;
use October\Rain\Database\Traits\NestedTree;
use October\Rain\Database\Traits\SoftDelete;
use October\Rain\Database\Traits\Validation;

class Comment extends Model
{
    use Validation, SoftDelete, NestedTree;

    protected $dates = ['deleted_at'];

    public $table = 'eev_blog_comments';

    public $rules = [
        'content' => [
            'required',
            'string',
            'max:65535'
        ],
        'post_id' => [
            'required',
            'integer',
        ],
        'user_id' => [
            'integer',
            'nullable',
        ],
        'is_moderated' => [
            'boolean',
        ],
        'parent_id' => [
            'nullable',
            'integer',
        ],
    ];

    public $belongsTo = [
        'post' => [
            Post::class,
            'key' => 'post_id',
            'otherKey' => 'id',
        ],
        'user' => [
            User::class,
            'key' => 'user_id',
            'otherKey' => 'id',
        ],
    ];

    public function getTitleAttribute() {
        return $this->id . ' - '. $this->post->title . ' - ' . $this->user->nameWithEmail;
    }
}
