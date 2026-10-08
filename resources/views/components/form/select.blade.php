@props(['name', 'label' => null, 'options' => [], 'selected' => null, 'placeholder' => null, 'required' => false])

<div class="form-group @error($name) has-error @enderror">
    <label for="{{ $name }}">
        {{ $label ?? __(str($name)->replace('_', ' ')->title()->toString()) }}
        @if ($required) <span class="text-danger">*</span> @endif
    </label>
    <select id="{{ $name }}" name="{{ $name }}" @required($required) {{ $attributes->merge(['class' => 'form-control']) }}>
        @if ($placeholder !== false)
            <option value="">{{ $placeholder ?? __('-- Select --') }}</option>
        @endif
        @foreach ($options as $key => $text)
            <option value="{{ $key }}" @selected((string) old($name, $selected) === (string) $key)>{{ $text }}</option>
        @endforeach
    </select>
    @error($name)
        <span class="help-block">{{ $message }}</span>
    @enderror
</div>
