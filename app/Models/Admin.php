<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Permission\Traits\HasRoles;

class Admin extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use SoftDeletes;
    use HasRoles;

    protected $guarded = [];

    protected $hidden = [
        'password',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected $casts = [
        'password' => 'hashed',
        'two_fa_secret' => 'encrypted',
    ];

    public function socials()
    {
        return $this->hasMany(Social::class);
    }
}
