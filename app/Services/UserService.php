<?php

namespace App\Services;


use App\Models\User;

class UserService
{
    public function get()
    {
        $query = User::get();

        return $query;
    }
}
