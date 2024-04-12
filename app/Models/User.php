<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    use HasFactory;
    protected $table = 'users';
    protected $guarded = [];

    public const TYPE_ADMIN = 'admin';
    public const TYPE_USER = 'user';
    public const ACTIVE = 1;
    public const INACTIVE = 0;
}
