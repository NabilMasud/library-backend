<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Inertia\Inertia;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles', 'permissions')->get();
        $roles = Role::all();
        $permissions = Permission::all();
        return Inertia::render('Admin/Users', [
            'users' => $users,
            'roles' => $roles,
            'permissions' => $permissions,
        ]);
    }

    public function updateRoles(Request $request, User $user)
    {
        $request->validate([
            'roles' => 'array',
            'roles.*' => 'exists:roles,name',
        ]);

        $user->syncRoles($request->roles);
        return back()->with('success', 'Role pengguna diperbarui.');
    }

    public function updatePermissions(Request $request, User $user)
    {
        $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name',
        ]);

        $user->syncPermissions($request->permissions);

        return back()->with('success', 'Izin pengguna berhasil diperbarui.');
    }
}
