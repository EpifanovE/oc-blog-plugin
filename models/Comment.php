<?php namespace EEV\Blog\Models;

use Flash;
use Model;
use October\Rain\Auth\Models\User;
use October\Rain\Database\Traits\NestedTree;
use October\Rain\Database\Traits\SoftDelete;
use October\Rain\Database\Traits\Validation;
use October\Rain\Exception\ValidationException;

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
//        'post_id' => [
//            'required',
//            'integer',
//        ],
//        'user_id' => [
//            'required',
//            'integer',
//        ],
        'is_moderated' => [
            'boolean',
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

    public function beforeSave()
    {
//        $this->checkParent();
    }

    private function checkParent() {
        if (!empty($this->parent_id)) {
            $parent = Comment::find($this->parent_id);
            if ($parent->post->id !== $this->post->id) {
                throw new ValidationException(['post_id' => 'Invalid parent']);
            }
        }
    }
}
