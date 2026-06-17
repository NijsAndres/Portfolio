@props([
    'name',
    'label',
    'model' => null,
    'required' => false,
    'type' => 'text',
    'placeholder' => '',
    'max' => null,
])

{{--
    Single-line text field rendered once per locale (English + Dutch). Each input
    submits as `name[en]` / `name[nl]`, which spatie/laravel-translatable stores
    as one JSON column. Only English is ever required; a blank Dutch value falls
    back to English on the frontend, so the stored value is read without fallback
    (getTranslation(..., false)) to show what is actually translated.
--}}
@php
    $locales = ['en' => 'English', 'nl' => 'Nederlands'];
    $valueFor = fn (string $locale) => old(
        "{$name}.{$locale}",
        $model?->getTranslation($name, $locale, false) ?? ''
    );
@endphp

<div>
    <label class="form-label">
        {{ $label }}
        @if ($required)
            <span class="text-brand-500">*</span>
        @endif
    </label>
    <div class="grid gap-x-5 gap-y-3 sm:grid-cols-2">
        @foreach ($locales as $code => $language)
            <div>
                <span class="mb-1 block text-xs font-medium uppercase tracking-wide text-ink/40">{{ $language }}</span>
                <input type="{{ $type }}"
                       id="{{ $name }}_{{ $code }}"
                       name="{{ $name }}[{{ $code }}]"
                       @if ($required && $code === 'en') required @endif
                       @if ($max) maxlength="{{ $max }}" @endif
                       @if ($placeholder) placeholder="{{ $placeholder }}" @endif
                       value="{{ $valueFor($code) }}"
                       class="form-input">
            </div>
        @endforeach
    </div>
</div>
