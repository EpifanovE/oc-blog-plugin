<?php

return [
    'comments_enable' => true,
    'guest_comments' => true,
    'moderation_before_publishing' => true,
    'show_all_posts' => true,
    'post' => [ // content, thumb, meta (date, author, comments), comments, categories, tags
        'layout' => [
            'thumb',
//            'meta' => [
//                'date',
//                'author',
//                'comments',
//            ],
            'content',
            'categories',
            'tags',
            'comments',
        ],
    ],
];