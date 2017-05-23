<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->increments('id');
            $table->string('short_id', 8)->unique();
            $table->integer('child_id')->unsigned();
            $table->string('quote')->nullable();
            $table->string('story', 1000)->nullable();
            $table->integer('font_id')->unsigned()->nullable();
            $table->longText('lqip');
            $table->string('img_original_url_id')->nullable();
            $table->string('img_baked_url_id')->nullable();
            $table->integer('preset_id')->unsigned()->nullable();
            $table->boolean('is_shared')->default(false);
            $table->boolean('is_printed')->default(false);
            $table->boolean('is_memory');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('child_id')->references('id')->on('children')->onDelete('cascade');
            $table->foreign('preset_id')->references('id')->on('presets')->onDelete('cascade');
            $table->foreign('font_id')->references('id')->on('fonts')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
}
