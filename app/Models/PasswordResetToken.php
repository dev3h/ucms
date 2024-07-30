<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordResetToken extends Model
{
    use HasFactory;

    protected $guarded = [];

    public $incrementing = false;

    protected $keyType = 'string';

    protected $primaryKey = 'email';
}
