<?php namespace EEV\Blog\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class CommentsController extends Controller
{
    public $implement = [
        'Backend\Behaviors\ListController',
        'Backend\Behaviors\FormController',
    ];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';

    public $requiredPermissions = [
        'eev.blog.comments'
    ];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('EEV.Blog', 'blog', 'comments');
    }

}
