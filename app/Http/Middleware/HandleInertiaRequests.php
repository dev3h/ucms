<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = null;
        if (Auth::check()) {
            $user =  Auth::user();
            $user->two_factor_enabled = $user?->two_factor_enabled === 1 ? false : true;
        }
        if ($user) {
            $role = ($user->roles()->first())?->name;
            $permissions = $user->getAllPermissions()?->pluck('name');
        }

        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $user,
                'role' => $role ?? null,
                'permissions' => $permissions ?? null,
            ],
        ]);
    }
}
