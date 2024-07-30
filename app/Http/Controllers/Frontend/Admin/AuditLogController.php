<?php

namespace App\Http\Controllers\Frontend\Admin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class AuditLogController extends Controller
{
    public function index()
    {
        return Inertia::render('AuditLog/Index');
    }
}
