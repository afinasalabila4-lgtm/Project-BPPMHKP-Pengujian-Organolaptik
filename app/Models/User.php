<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;


    protected $fillable = [
        'name',
        'nip',
        'username',
        'password',
        'role',
        'status',
    ];


    protected $hidden = [
        'password',
        'remember_token',
    ];


    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }


    // ==========================
    // ROLE CHECKING
    // ==========================

    public function isAdmin()
    {
        return $this->role === 'admin';
    }


    public function isPanelis()
    {
        return $this->role === 'panelis';
    }


    public function isPenyelia()
    {
        return $this->role === 'penyelia';
    }

public function sessionUsers()
{
    return $this->hasMany(SessionUser::class);
}

}