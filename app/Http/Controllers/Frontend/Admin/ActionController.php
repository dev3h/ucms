<?php

namespace App\Http\Controllers\Frontend\Admin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class ActionController extends Controller
{
    public function index()
    {
        return Inertia::render('Action/Index');
    }
}
