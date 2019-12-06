<?php namespace EEV\Blog\Models;

use Model;

class Settings extends Model
{
    public $implement = ['System.Behaviors.SettingsModel'];

    public $settingsCode = 'eev_blog_settings';

    public $settingsFields = 'fields.yaml';
}