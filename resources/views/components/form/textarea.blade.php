@props(['name', 'label' => null, 'value' => null, 'rows' => 3])

<div class="form-group @error($name) has-error @enderror">
    <label for="{{ $name }}">{{ $label ?? __(str($name)->replace('_', ' ')->title()->toString()) }}</label>
    <textarea id="{{ $name }}" name="{{ $name }}" rows="{{ $rows }}" {{ $attributes->merge(['class' => 'form-control']) }}>{{ old($name, $value) }}</textarea>
    @error($name)
        <span class="help-block">{{ $message }}</span>
    @enderror
</div>
