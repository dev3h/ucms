<?php

namespace App\Http\Controllers\Frontend\Admin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class SubSystemController extends Controller
{
    public function index()
    {
        return Inertia::render('SubSystem/Index');
    }

    public function show($id)
    {
        return Inertia::render('SubSystem/Show', [
            'id' => +$id
        ]);
    }
}
