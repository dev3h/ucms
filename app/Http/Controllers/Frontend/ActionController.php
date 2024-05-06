<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ActionController extends Controller
{
    public function index()
    {
        $this->authorize('view', User::class);
        return Inertia::render('Action/Index');
    }
}
