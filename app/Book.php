<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{

    protected $fillable = ['title', 'author_first_name', 'author_last_name', 'user_id'];

    public function notes()
    {
        return $this->hasMany(Note::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tags() 
    {
    	return $this->belongsToMany(Tag::class);
    }

}
