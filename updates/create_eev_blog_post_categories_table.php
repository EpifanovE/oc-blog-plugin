<?php namespace EEV\Blog\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class CreateEevBlogPostCategoriesTable extends Migration
{
    public function up()
    {
        Schema::create('eev_blog_post_categories', function($table)
        {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->string('title', 1024)->nullable();
            $table->text('content')->nullable();
            $table->string('preview', 1024)->nullable();
            $table->string('slug', 1024)->nullable();
            $table->string('image', 1024)->nullable();
            $table->string('status', 16)->nullable();
            $table->string('seo_title', 1024)->nullable();
            $table->string('seo_description', 2048)->nullable();
            $table->string('seo_keywords', 1024)->nullable();
            $table->integer('parent_id')->nullable();
            $table->integer('nest_left')->nullable();
            $table->integer('nest_right')->nullable();
            $table->integer('nest_depth')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();
            $table->timestamp('deleted_at')->nullable();
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('eev_blog_post_categories');
    }
}
