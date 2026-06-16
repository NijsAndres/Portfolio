@props([
    'name',
    'label',
    'model' => null,
    'rows' => 4,
    'rich' => false,
])

{{--
    Multi-line text field rendered once per locale (English + Dutch), submitting
    as `name[en]` / `name[nl]` for spatie/laravel-translatable. With :rich the
    bold WYSIWYG editor from resources/js/app.js is wired up: each locale gets its
    own contenteditable (id `<name>_<locale>_editor`) paired to the textarea via
    data-rich-field, so the existing generic initRichEditors() handles both. The
    stored value is read without fallback so untranslated locales show empty.
--}}
@php
    $locales = ['en' => 'English', 'nl' => 'Nederlands'];
    $valueFor = fn (string $locale) => old(
        "{$name}.{$locale}",
        $model?->getTranslation($name, $locale, false) ?? ''
    );
@endphp

<div>
    <label class="form-label">{{ $label }}</label>
    <div class="grid gap-x-5 gap-y-4 sm:grid-cols-2">
        @foreach ($locales as $code => $language)
            @php $id = "{$name}_{$code}"; @endphp
            <div>
                <div class="mb-1 flex items-center justify-between gap-3">
                    <span class="text-xs font-medium uppercase tracking-wide text-ink/40">{{ $language }}</span>
                    @if ($rich)
                        <button type="button" data-bold-target="{{ $id }}_editor"
                                class="inline-flex h-7 w-7 shrink-0 items-center justify-center rounded-lg border border-ink/15 font-bold text-ink/70 hover:bg-ink/5 hover:text-ink transition-colors"
                                title="Bold (⌘/Ctrl+B)" aria-label="Toggle bold">B</button>
                    @endif
                </div>
                @if ($rich)
                    <div id="{{ $id }}_editor" data-rich-field="{{ $id }}" contenteditable="true"
                         class="form-input hidden border px-3 py-2 min-h-36 leading-relaxed focus:outline-none"></div>
                @endif
                <textarea id="{{ $id }}" name="{{ $name }}[{{ $code }}]" rows="{{ $rows }}"
                          class="form-input">{{ $valueFor($code) }}</textarea>
            </div>
        @endforeach
    </div>
</div>
