<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Inertia\Inertia;
use App\Models\User;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    public function index()
    {
        $users = User::all();
        $roles = Role::all();
        $permissions = Permission::with('roles', 'users')->get();
        return Inertia::render('Admin/Permissions', [
            'permissions' => $permissions,
            'roles' => $roles,
            'users' => $users,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|unique:permissions']);
        Permission::create(['name' => $request->name]);
        return back()->with('success', 'Permission berhasil dibuat.');
    }

    public function update(Request $request, Permission $permission)
    {
        $request->validate(['name' => 'required|unique:permissions,name,' . $permission->id]);
        $permission->update(['name' => $request->name]);
        return back()->with('success', 'Permission berhasil diperbarui.');
    }

    public function destroy(Permission $permission)
    {
        $permission->delete();
        return back()->with('success', 'Permission berhasil dihapus.');
    }

    public function updateUsers(Request $request, Permission $permission)
    {
        $request->validate([
            'users' => 'array',
            'users.*' => 'exists:users,name',
        ]);

        // foreach ($request->users as $userName) {
        //     $user = User::where('name', $userName)->first();
        //     if ($user) {
        //         $user->givePermissionTo($permission->name);
        //     }
        // }

        $userIds = User::whereIn('name', $request->users)->pluck('id');
        $permission->users()->sync($userIds);

        return back()->with('success', 'User berhasil diperbarui untuk permission ini.');
    }

    public function updateRoles(Request $request, Permission $permission)
    {
        $request->validate([
            'roles' => 'array',
            'roles.*' => 'exists:roles,name',
        ]);

        $permission->syncRoles($request->roles);

        return back()->with('success', 'Role berhasil diperbarui untuk permission ini.');
    }
}
