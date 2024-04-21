<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Module extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $guarded = [];

     public function subsystem()
    {
        return $this->belongsTo(SubSystem::class, 'sub_system_id');
    }

    public function actions()
    {
        return $this->hasMany(Action::class);
    }
}
