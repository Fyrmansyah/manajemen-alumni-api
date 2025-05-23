<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use HasApiTokens;

    protected $guarded = ['id'];
    protected $hidden = ['password'];


    // -------------------------------------
    //          MODEL METHODS
    // -------------------------------------


    // -------------------------------------
    //          OVERRIDE METHODS
    // -------------------------------------
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }


    // -------------------------------------
    //          RELATION METHODS
    // -------------------------------------
}
