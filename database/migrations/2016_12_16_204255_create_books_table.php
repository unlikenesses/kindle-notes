<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBooksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('books', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title_string');
            $table->string('title')->nullable();
            $table->string('author_first_name')->nullable();
            $table->string('author_last_name')->nullable();
            $table->string('year')->nullable();
            $table->integer('user_id')->unsigned()->index();
            $table->timestamps();
            $table->softDeletes();
        });
        // DB::statement('ALTER TABLE books ADD FULLTEXT full(title, author_first_name, author_last_name, year)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('books');
    }
}
