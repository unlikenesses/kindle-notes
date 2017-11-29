<?php

namespace App;

use Laravel\Scout\Searchable;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use Searchable; 

	protected $fillable = ['book_id', 'page', 'location', 'date', 'note', 'type'];

    public function book()
    {
        return $this->belongsTo(Book::class);
    }
}
