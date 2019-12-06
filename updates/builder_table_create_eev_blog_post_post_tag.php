<?php namespace EEV\Blog\Updates;

use Schema;
use October\Rain\Database\Updates\Migration;

class BuilderTableCreateEevBlogPostPostTag extends Migration
{
    public function up()
    {
        Schema::create('eev_blog_post_post_tag', function($table)
        {
            $table->engine = 'InnoDB';
            $table->integer('post_id')->unsigned();
            $table->integer('post_tag_id')->unsigned();
            $table->primary(['post_id','post_tag_id'], 'post_tag');
        });
    }
    
    public function down()
    {
        Schema::dropIfExists('eev_blog_post_post_tag');
    }
}
