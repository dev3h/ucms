<?php

namespace App\Http\Controllers\Frontend\Admin;

use App\Http\Controllers\Controller;

class ProfileController extends Controller
{
    public function index()
    {
        return inertia('MyProfile/Index');
    }
}
