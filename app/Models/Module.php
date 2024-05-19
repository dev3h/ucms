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

     public function subsystems()
    {
        return $this->belongsToMany(SubSystem::class, 'subsystem_modules');
    }

    public function actions()
    {
        return $this->belongsToMany(Action::class, 'module_actions');
    }
}
