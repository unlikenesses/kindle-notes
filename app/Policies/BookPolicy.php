<?php

namespace App\Policies;

use App\Book;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BookPolicy
{
    use HandlesAuthorization;

    public function view(User $user, Book $book)
    {
        return $book->user_id == $user->id;
    }
}
