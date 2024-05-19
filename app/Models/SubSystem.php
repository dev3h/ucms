<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubSystem extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'sub_systems';
    protected $guarded = [];

    public function system()
    {
        return $this->belongsTo(System::class);
    }

    public function modules()
    {
        return $this->belongsToMany(Module::class, 'subsystem_modules');
    }
}
