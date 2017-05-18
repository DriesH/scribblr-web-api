<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQuotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('quotes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('short_id', 8)->unique();
            $table->integer('child_id')->unsigned();
            $table->string('quote');
            $table->string('story')->nullable();
            $table->string('img_main_color')->nullable();
            $table->longText('lqip')->nullable();
            $table->string('img_original_url_id')->nullable();
            $table->string('img_baked_url_id')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('child_id')->references('id')->on('children')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('quotes');
    }
}
