<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
	public function getRouteKeyName()
	{
	    return 'slug';
	}
	
    public function books() 
    {
    	return $this->belongsToMany(Book::class);
    }
}
