<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\Authenticatable as AuthenticatableTrait;

class User extends Model implements Authenticatable
{
    use AuthenticatableTrait;
    use HasApiTokens;
    use HasFactory;

    protected $table = 'users';
    protected $fillable = [
        'email',
        'password',
        'registration_date',
        'user_streak',
        'timezone',
    ];
}