<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notice extends Model
{
    use HasFactory;
    use SoftDeletes;

    public const SENDER_ALL = 1;
    public const SENDER_OPTION = 2;

    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'user_ids' => 'array',
    ];

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}
