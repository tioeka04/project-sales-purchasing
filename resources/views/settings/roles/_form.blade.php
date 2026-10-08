@php
    $isAdminRole = isset($role) && $role->name === \Database\Seeders\RolePermissionSeeder::ADMIN;
    $granted = old('permissions', isset($role) ? $role->permissions->pluck('name')->all() : []);
@endphp

<form method="POST" action="{{ $action }}">
    @csrf
    @isset($role)
        @method('PUT')
    @endisset

    <x-form.input name="name" :label="__('Role')" :value="$role->name ?? null" maxlength="50" :readonly="$isAdminRole" required />

    <h4>{{ __('Permissions') }}</h4>
    @if ($isAdminRole)
        <p class="text-muted">{{ __('The Admin role always has every permission.') }}</p>
    @endif

    <div class="row">
        @foreach ($permissionGroups as $group => $names)
            <div class="col-md-3 col-sm-6">
                <div class="x_panel" style="padding-bottom: 5px;">
                    <strong>{{ __(str($group)->replace('-', ' ')->title()->toString()) }}</strong>
                    @foreach ($names as $name)
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="permissions[]" value="{{ $name }}"
                                       @checked($isAdminRole || in_array($name, $granted, true)) @disabled($isAdminRole)>
                                {{ $name }}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> {{ __('Save') }}</button>
    <a href="{{ route('admin.settings.roles.index') }}" class="btn btn-default">{{ __('Cancel') }}</a>
</form>
