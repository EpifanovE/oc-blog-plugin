<?php namespace EEV\Blog\Controllers;

use Backend\Classes\Controller;
use BackendMenu;

class PostCategoriesController extends Controller
{
    public $implement = [
        'Backend\Behaviors\ListController',
        'Backend\Behaviors\FormController',
        'Backend\Behaviors\ReorderController',
    ];

    public $listConfig = 'config_list.yaml';
    public $formConfig = 'config_form.yaml';
    public $reorderConfig = 'config_reorder.yaml';

    public $requiredPermissions = [
        'eev.blog.categories'
    ];

    public function __construct()
    {
        parent::__construct();
        BackendMenu::setContext('EEV.Blog', 'blog', 'categories');
    }
}
