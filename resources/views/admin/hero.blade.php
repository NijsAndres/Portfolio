@extends('admin.layouts.app')

@section('title', 'Hero Section')

@section('content')
    <form method="POST" action="{{ route('admin.hero.update') }}" enctype="multipart/form-data" class="max-w-4xl space-y-6">
        @csrf
        @method('PUT')

        <div class="card p-6 space-y-5">
            <x-admin.translatable-input name="headline" label="Headline" :model="$hero" :required="true" :max="255" />
            <x-admin.translatable-input name="subheadline" label="Subheadline" :model="$hero" :max="255" />
            <x-admin.translatable-input name="tagline" label="Tagline" :model="$hero" :max="255" />

            <div class="grid gap-x-5 gap-y-5 sm:grid-cols-2">
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
            </div>

            @include('admin.media._picker', ['selected' => old('media_id', $hero->media_id), 'media' => $media])
        </div>

        <div class="flex justify-end">
            <button type="submit" class="btn-primary">
                Save changes
            </button>
        </div>
    </form>
@endsection
