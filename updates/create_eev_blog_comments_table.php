<?php namespace EEV\Blog\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateEevBlogComments extends Migration
{
    public function up()
    {
        Schema::create('eev_blog_comments', function ($table) {
            $table->engine = 'InnoDB';
            $table->increments('id')->unsigned();
            $table->text('content')->nullable();
            $table->integer('post_id')->nullable()->unsigned();
            $table->boolean('is_moderated')->nullable()->default(false);
            $table->integer('user_id')->nullable()->unsigned();
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
        Schema::dropIfExists('eev_blog_comments');
    }
}
