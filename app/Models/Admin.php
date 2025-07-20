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
    
    /**
     * Get the name of the unique identifier for the user.
     */
    public function getAuthIdentifierName()
    {
        return 'username';
    }
    
    /**
     * Get the unique identifier for the user.
     */
    public function getAuthIdentifier()
    {
        return $this->getKey(); // Returns the primary key (id)
    }
    
    /**
     * Get the unique identifier for the user for session storage.
     * This ensures session consistency.
     */
    public function getAuthIdentifierForBroadcasting()
    {
        return $this->getKey();
    }
    
    /**
     * Get the name of the user.
     */
    public function getAuthName()
    {
        return $this->nama ?? $this->username;
    }
    
    /**
     * Get the password for the user.
     */
    public function getAuthPassword()
    {
        return $this->password;
    }
    
    /**
     * Get the "remember me" token for the user.
     */
    public function getRememberToken()
    {
        return $this->remember_token ?? null;
    }
    
    /**
     * Set the "remember me" token for the user.
     */
    public function setRememberToken($value)
    {
        // Only set if column exists and value is provided
        if ($value && Schema::hasColumn('admins', 'remember_token')) {
            $this->remember_token = $value;
        }
    }
    
    /**
     * Get the name of the "remember me" token column.
     */
    public function getRememberTokenName()
    {
        return 'remember_token';
    }
    
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
