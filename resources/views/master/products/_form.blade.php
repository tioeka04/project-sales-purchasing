<form method="POST" action="{{ $action }}">
    @csrf
    @isset($product)
        @method('PUT')
    @endisset

    <x-form.input name="product_code" :label="__('Product Code')" :value="$product->product_code ?? null" maxlength="20" required />
    <x-form.input name="product_name" :label="__('Product Name')" :value="$product->product_name ?? null" maxlength="150" required />
    <x-form.input name="price" :label="__('Price')" type="number" step="0.01" min="0" :value="$product->price ?? null" required />
    <x-form.input name="stock" :label="__('Stock')" type="number" step="1" min="0" :value="$product->stock ?? 0" required />

    <button type="submit" class="btn btn-success"><i class="fa fa-save"></i> {{ __('Save') }}</button>
    <a href="{{ route('admin.master.products.index') }}" class="btn btn-default">{{ __('Cancel') }}</a>
</form>
