<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\StoreUserRequest;
use App\Http\Requests\Settings\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UserController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [new Middleware('permission:user.manage')];
    }

    public function index(Request $request): View
    {
        $term = $request->query('q');

        $users = User::with('roles')
            ->when($term, fn ($q) => $q->where(fn ($q) => $q->where('name', 'like', "%{$term}%")->orWhere('email', 'like', "%{$term}%")))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('settings.users.index', compact('users'));
    }

    public function create(): View
    {
        return view('settings.users.create', ['roles' => Role::orderBy('name')->pluck('name', 'name')]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $data = $request->validated();

        User::create($request->safe()->only(['name', 'email', 'password']))->assignRole($data['role']);

        return redirect()->route('admin.settings.users.index')->with('success', __('User saved.'));
    }

    public function edit(User $user): View
    {
        return view('settings.users.edit', [
            'user' => $user->load('roles'),
            'roles' => Role::orderBy('name')->pluck('name', 'name'),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        if (empty($data['password'])) {
            unset($data['password']);
        }

        $user->update(collect($data)->only(['name', 'email', 'password'])->all());
        $user->syncRoles($data['role']);

        return redirect()->route('admin.settings.users.index')->with('success', __('User updated.'));
    }

    public function destroy(Request $request, User $user): RedirectResponse
    {
        if ($user->is($request->user())) {
            return back()->with('error', __('You cannot delete your own account.'));
        }

        if ($user->hasCreatedOrders()) {
            return back()->with('error', __('User has created orders and cannot be deleted.'));
        }

        $user->delete();

        return redirect()->route('admin.settings.users.index')->with('success', __('User deleted.'));
    }
}
