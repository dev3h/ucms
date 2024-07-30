<?php

namespace App\Http\Controllers\Frontend\Admin;

use App\Http\Controllers\Controller;
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
