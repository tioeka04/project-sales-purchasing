<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\StoreRoleRequest;
use App\Http\Requests\Settings\UpdateRoleRequest;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [new Middleware('permission:role.manage')];
    }

    public function index(): View
    {
        return view('settings.roles.index', [
            'roles' => Role::withCount(['permissions', 'users'])->orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        return view('settings.roles.create', ['permissionGroups' => $this->permissionGroups()]);
    }

    public function store(StoreRoleRequest $request): RedirectResponse
    {
        $role = Role::create(['name' => $request->validated('name')]);
        $role->syncPermissions($request->validated('permissions') ?? []);

        return redirect()->route('admin.settings.roles.index')->with('success', __('Role saved.'));
    }

    public function edit(Role $role): View
    {
        return view('settings.roles.edit', [
            'role' => $role->load('permissions'),
            'permissionGroups' => $this->permissionGroups(),
        ]);
    }

    public function update(UpdateRoleRequest $request, Role $role): RedirectResponse
    {
        if (! $this->isProtected($role)) {
            $role->update(['name' => $request->validated('name')]);
        }

        // The Admin role always keeps every permission so nobody can lock themselves out.
        $role->syncPermissions($this->isProtected($role) ? Permission::all() : ($request->validated('permissions') ?? []));

        return redirect()->route('admin.settings.roles.index')->with('success', __('Role updated.'));
    }

    public function destroy(Role $role): RedirectResponse
    {
        if ($this->isProtected($role)) {
            return back()->with('error', __('The Admin role cannot be deleted.'));
        }

        if ($role->users()->exists()) {
            return back()->with('error', __('Role is still assigned to users and cannot be deleted.'));
        }

        $role->delete();

        return redirect()->route('admin.settings.roles.index')->with('success', __('Role deleted.'));
    }

    private function isProtected(Role $role): bool
    {
        return $role->name === RolePermissionSeeder::ADMIN;
    }

    /**
     * @return Collection<string, Collection> permissions grouped by resource.
     */
    private function permissionGroups()
    {
        return Permission::orderBy('name')->pluck('name')
            ->groupBy(fn (string $name) => explode('.', $name)[0]);
    }
}
