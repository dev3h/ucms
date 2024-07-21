<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ModuleController extends Controller
{
    public function index()
    {
        return Inertia::render('Module/Index');
    }

    public function show($id)
    {
        return Inertia::render('Module/Show', [
            'id' => +$id
        ]);
    }
}
