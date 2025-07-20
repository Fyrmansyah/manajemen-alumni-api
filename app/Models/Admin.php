<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Schema;

class Admin extends Authenticatable
{
    use HasApiTokens;

    protected $guarded = ['id'];
    protected $hidden = ['password'];
    protected $fillable = ['nama', 'username', 'password', 'remember_token'];


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
