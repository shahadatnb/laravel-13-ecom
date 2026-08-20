<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::adminStaff()->with('roles')->orderBy('name')->get();

        return view('admin.settings.users', compact('users'));
    }

    public function create(): View
    {
        $roles = Role::orderBy('name')->get();
        $permissions = Permission::orderBy('name')->get();

        return view('admin.settings.user-create', compact('roles', 'permissions'));
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['type'] = $data['type'] ?? User::TYPE_STAFF;

        $user = User::create($data);
        $user->assignRole($request->input('roles', []));
        $user->syncPermissions($request->input('permissions', []));

        return redirect()->route('admin.settings.users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user): View
    {
        $roles = Role::orderBy('name')->get();
        $permissions = Permission::orderBy('name')->get();
        $user->load('roles');

        return view('admin.settings.user-edit', compact('user', 'roles', 'permissions'));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->safe()->only(['name', 'email', 'phone', 'address', 'date_of_birth', 'gender', 'timezone', 'locale', 'type']);
        $user->update($data);

        if ($request->filled('password')) {
            $user->update(['password' => $request->validated('password')]);
        }

        $user->syncRoles($request->input('roles', []));
        $user->syncPermissions($request->input('permissions', []));

        return redirect()->route('admin.settings.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return redirect()->route('admin.settings.users.index')->with('success', 'User deleted successfully.');
    }
}
