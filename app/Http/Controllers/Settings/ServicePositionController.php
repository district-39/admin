<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ServicePositionController extends Controller
{
    public function edit(Request $request): Response
    {
        return Inertia::render('settings/ServicePositions', [
            'userRoles' => $request->user()->roles,
            'availableRoles' => Role::query()->orderBy('name')->get(),
        ]);
    }

    public function addRole(Request $request): RedirectResponse
    {
        $request->validate([
            'role_id' => ['required', 'exists:roles,id'],
        ]);

        $request->user()->roles()->syncWithoutDetaching([$request->role_id]);

        return to_route('service-positions.edit');
    }

    public function removeRole(Request $request, Role $role): RedirectResponse
    {
        $request->user()->roles()->detach($role);

        return to_route('service-positions.edit');
    }
}
