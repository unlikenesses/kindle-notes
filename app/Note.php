<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
	protected $fillable = ['book_id', 'page', 'location', 'date', 'note', 'type'];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
