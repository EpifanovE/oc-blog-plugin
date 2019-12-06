<?php namespace EEV\Blog\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateEevBlogPostPostCategory extends Migration
{
    public function up()
    {
        Schema::create('eev_blog_post_post_category', function($table)
        {
            $table->engine = 'InnoDB';
            $table->integer('post_id')->unsigned();
            $table->integer('post_category_id')->unsigned();
            $table->primary(['post_id', 'post_category_id'], 'post_category');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('eev_blog_post_post_category');
    }
}
