<form method="POST" action="{{ $action }}">
    @csrf
    @isset($user)
        @method('PUT')
    @endisset

    <x-form.input name="name" :label="__('Name')" :value="$user->name ?? null" maxlength="100" required />
    <x-form.input name="email" :label="__('Email')" type="email" :value="$user->email ?? null" maxlength="100" required />
    <x-form.select name="role" :label="__('Role')" :options="$roles" :selected="isset($user) ? $user->roles->first()?->name : null" required />

    <x-form.input name="password" :label="__('Password')" type="password" autocomplete="new-password" :required="! isset($user)" />
    @isset($user)
        <p class="help-block" style="margin-top: -10px;">{{ __('Leave blank to keep the current password.') }}</p>
    @endisset
    <x-form.input name="password_confirmation" :label="__('Confirm Password')" type="password" autocomplete="new-password" :required="! isset($user)" />

    <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> {{ __('Save') }}</button>
    <a href="{{ route('admin.settings.users.index') }}" class="btn btn-default">{{ __('Cancel') }}</a>
</form>
