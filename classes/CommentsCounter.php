<?php

namespace EEV\Blog\Classes;

use EEV\Blog\Models\Comment;

class CommentsCounter
{

    public static function count() {
        return Comment::where('is_moderated', false)->count();
    }

}