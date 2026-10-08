{{-- Expects: $index (int|'__INDEX__'), $line (array), $products --}}
<tr class="line-row">
    <td @class(['has-error' => $errors->has("lines.$index.product_id")])>
        <select name="lines[{{ $index }}][product_id]" class="form-control line-product" required>
            <option value="">{{ __('-- Select --') }}</option>
            @foreach ($products as $product)
                <option value="{{ $product->id }}" data-price="{{ $product->price }}" @selected((string) ($line['product_id'] ?? '') === (string) $product->id)>
                    {{ $product->product_code }} - {{ $product->product_name }}
                </option>
            @endforeach
        </select>
        @error("lines.$index.product_id") <span class="help-block">{{ $message }}</span> @enderror
    </td>
    <td @class(['has-error' => $errors->has("lines.$index.quantity")])>
        <input type="number" name="lines[{{ $index }}][quantity]" class="form-control line-qty" min="1" step="1" value="{{ $line['quantity'] ?? 1 }}" required>
        @error("lines.$index.quantity") <span class="help-block">{{ $message }}</span> @enderror
    </td>
    <td @class(['has-error' => $errors->has("lines.$index.price")])>
        <input type="number" name="lines[{{ $index }}][price]" class="form-control line-price" min="0" step="0.01" value="{{ $line['price'] ?? '' }}" required>
        @error("lines.$index.price") <span class="help-block">{{ $message }}</span> @enderror
    </td>
    <td @class(['has-error' => $errors->has("lines.$index.discount")])>
        <input type="number" name="lines[{{ $index }}][discount]" class="form-control line-discount" min="0" max="100" step="0.01" value="{{ $line['discount'] ?? 0 }}" required>
        @error("lines.$index.discount") <span class="help-block">{{ $message }}</span> @enderror
    </td>
    <td class="text-right line-total" style="vertical-align: middle;">0.00</td>
    <td class="text-center" style="vertical-align: middle;">
        <button type="button" class="btn btn-xs btn-danger remove-line" title="{{ __('Remove') }}"><i class="fa fa-times"></i></button>
    </td>
</tr>
