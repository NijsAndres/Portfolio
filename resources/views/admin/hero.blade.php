@extends('admin.layouts.app')

@section('title', 'Hero Section')

@section('content')
    <form method="POST" action="{{ route('admin.hero.update') }}" enctype="multipart/form-data" class="max-w-2xl space-y-6">
        @csrf
        @method('PUT')

        <div class="card p-6 space-y-5">
            <div>
                <label for="headline" class="form-label">Headline <span class="text-brand-500">*</span></label>
                <input type="text" id="headline" name="headline" required
                       value="{{ old('headline', $hero->headline) }}"
                       class="form-input">
            </div>

            <div>
                <label for="subheadline" class="form-label">Subheadline</label>
                <input type="text" id="subheadline" name="subheadline"
                       value="{{ old('subheadline', $hero->subheadline) }}"
                       class="form-input">
            </div>

            <div>
                <label for="tagline" class="form-label">Tagline</label>
                <input type="text" id="tagline" name="tagline"
                       value="{{ old('tagline', $hero->tagline) }}"
                       class="form-input">
            </div>

            {{-- Skills repeater (skills[]) --}}
            <div x-data="{ items: @js(array_values(old('skills', $hero->skills ?? []))) }">
                <label class="form-label">Skills</label>
                <template x-for="(item, idx) in items" :key="idx">
                    <div class="flex gap-2 mb-2">
                        <input type="text" name="skills[]" x-model="items[idx]"
                               class="form-input flex-1">
                        <button type="button" @click="items.splice(idx, 1)"
                                class="px-3 text-sm font-semibold text-red-600 hover:text-red-700">Remove</button>
                    </div>
                </template>
                <button type="button" @click="items.push('')"
                        class="mt-1 text-sm font-semibold text-brand-700 hover:text-brand-800">+ Add skill</button>
            </div>

            {{-- Disciplines repeater (disciplines[]) --}}
            <div x-data="{ items: @js(array_values(old('disciplines', $hero->disciplines ?? []))) }">
                <label class="form-label">Disciplines</label>
                <template x-for="(item, idx) in items" :key="idx">
                    <div class="flex gap-2 mb-2">
                        <input type="text" name="disciplines[]" x-model="items[idx]"
                               class="form-input flex-1">
                        <button type="button" @click="items.splice(idx, 1)"
                                class="px-3 text-sm font-semibold text-red-600 hover:text-red-700">Remove</button>
                    </div>
                </template>
                <button type="button" @click="items.push('')"
                        class="mt-1 text-sm font-semibold text-brand-700 hover:text-brand-800">+ Add discipline</button>
            </div>

            <div>
                <label for="image" class="form-label">Image</label>
                @if ($hero->image_url)
                    <img src="{{ $hero->image_url }}" alt="Current hero image"
                         class="mb-2 h-32 w-auto rounded-lg border border-ink/10 object-cover">
                @endif
                <input type="file" id="image" name="image" accept="image/*"
                       class="w-full text-sm text-ink/70 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-brand-700 hover:file:bg-brand-100">
                <p class="mt-1 text-xs text-ink/40">JPG, PNG or WebP, up to 4 MB. Leave empty to keep the current image.</p>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="btn-primary">
                Save changes
            </button>
        </div>
    </form>
@endsection
