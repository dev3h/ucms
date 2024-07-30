<?php

namespace App\Http\Controllers\Frontend\Admin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class SystemController extends Controller
{
    public function index()
    {
//        $this->authorize('view', User::class);
        return Inertia::render('System/Index');
    }
}
