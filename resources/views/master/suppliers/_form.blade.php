<form method="POST" action="{{ $action }}">
    @csrf
    @isset($supplier)
        @method('PUT')
    @endisset

    <x-form.input name="supplier_code" :label="__('Supplier Code')" :value="$supplier->supplier_code ?? null" maxlength="20" required />
    <x-form.input name="name" :label="__('Name')" :value="$supplier->name ?? null" maxlength="150" required />
    <x-form.input name="phone" :label="__('Phone')" :value="$supplier->phone ?? null" maxlength="20" />
    <x-form.input name="email" :label="__('Email')" type="email" :value="$supplier->email ?? null" maxlength="100" />
    <x-form.textarea name="address" :label="__('Address')" :value="$supplier->address ?? null" />

    <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> {{ __('Save') }}</button>
    <a href="{{ route('admin.master.suppliers.index') }}" class="btn btn-default">{{ __('Cancel') }}</a>
</form>
