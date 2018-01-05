<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('book_id')->unsigned()->index();
            $table->string('page')->nullable();
            $table->string('location')->nullable();
            $table->string('date')->nullable();
            $table->text('note')->nullable();
            $table->integer('type')->unsigned();
            $table->integer('user_id')->unsigned()->index();
            $table->timestamps();
            $table->softDeletes();
        });
        // DB::statement('ALTER TABLE notes ADD FULLTEXT full(page, location, note, date)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notes');
    }
}
