<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->increments('id');
            $table->string('tag')->unique();
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('book_tag', function (Blueprint $table) {
            $table->integer('book_id')->unsigned();
            $table->integer('tag_id')->unsigned();
            $table->primary(['book_id', 'tag_id']);
        });

        Schema::create('tag_user', function (Blueprint $table) {
            $table->integer('user_id')->unsigned();
            $table->integer('tag_id')->unsigned();
            $table->primary(['user_id', 'tag_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tags');
        Schema::dropIfExists('book_tag');
        Schema::dropIfExists('tag_user');
    }
}
