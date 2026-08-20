<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePermissionRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index(): View
    {
        $permissions = Permission::orderBy('name')->get();

        return view('admin.settings.permissions', compact('permissions'));
    }

    public function create(): View
    {
        return view('admin.settings.permission-create');
    }

    public function store(StorePermissionRequest $request): RedirectResponse
    {
        Permission::create(['name' => $request->validated('name')]);

        return redirect()->route('admin.settings.permissions.index')->with('success', 'Permission created successfully.');
    }

    public function destroy(Permission $permission): RedirectResponse
    {
        $permission->delete();

        return redirect()->route('admin.settings.permissions.index')->with('success', 'Permission deleted successfully.');
    }
}
