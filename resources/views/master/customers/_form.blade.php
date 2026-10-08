<form method="POST" action="{{ $action }}">
    @csrf
    @isset($customer)
        @method('PUT')
    @endisset

    <x-form.input name="customer_code" :label="__('Customer Code')" :value="$customer->customer_code ?? null" maxlength="20" required />
    <x-form.input name="name" :label="__('Name')" :value="$customer->name ?? null" maxlength="150" required />
    <x-form.input name="phone" :label="__('Phone')" :value="$customer->phone ?? null" maxlength="20" />
    <x-form.input name="email" :label="__('Email')" type="email" :value="$customer->email ?? null" maxlength="100" />
    <x-form.textarea name="address" :label="__('Address')" :value="$customer->address ?? null" />

    <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> {{ __('Save') }}</button>
    <a href="{{ route('admin.master.customers.index') }}" class="btn btn-default">{{ __('Cancel') }}</a>
</form>
