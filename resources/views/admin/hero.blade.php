@extends('admin.layouts.app')

@section('title', 'Hero Section')

@section('content')
    <form method="POST" action="{{ route('admin.hero.update') }}" class="max-w-2xl space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-lg border border-gray-200 p-6 space-y-5">
            <div>
                <label for="headline" class="block text-sm font-medium text-gray-700 mb-1">Headline <span class="text-red-500">*</span></label>
                <input type="text" id="headline" name="headline" required
                       value="{{ old('headline', $hero->headline) }}"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>

            <div>
                <label for="subheadline" class="block text-sm font-medium text-gray-700 mb-1">Subheadline</label>
                <input type="text" id="subheadline" name="subheadline"
                       value="{{ old('subheadline', $hero->subheadline) }}"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>

            <div>
                <label for="tagline" class="block text-sm font-medium text-gray-700 mb-1">Tagline</label>
                <input type="text" id="tagline" name="tagline"
                       value="{{ old('tagline', $hero->tagline) }}"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>

            {{-- Skills repeater (skills[]) --}}
            <div x-data="{ items: @js(array_values(old('skills', $hero->skills ?? []))) }">
                <label class="block text-sm font-medium text-gray-700 mb-1">Skills</label>
                <template x-for="(item, idx) in items" :key="idx">
                    <div class="flex gap-2 mb-2">
                        <input type="text" name="skills[]" x-model="items[idx]"
                               class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <button type="button" @click="items.splice(idx, 1)"
                                class="px-3 text-sm text-red-600 hover:text-red-800">Remove</button>
                    </div>
                </template>
                <button type="button" @click="items.push('')"
                        class="mt-1 text-sm font-medium text-indigo-600 hover:text-indigo-800">+ Add skill</button>
            </div>

            {{-- Disciplines repeater (disciplines[]) --}}
            <div x-data="{ items: @js(array_values(old('disciplines', $hero->disciplines ?? []))) }">
                <label class="block text-sm font-medium text-gray-700 mb-1">Disciplines</label>
                <template x-for="(item, idx) in items" :key="idx">
                    <div class="flex gap-2 mb-2">
                        <input type="text" name="disciplines[]" x-model="items[idx]"
                               class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <button type="button" @click="items.splice(idx, 1)"
                                class="px-3 text-sm text-red-600 hover:text-red-800">Remove</button>
                    </div>
                </template>
                <button type="button" @click="items.push('')"
                        class="mt-1 text-sm font-medium text-indigo-600 hover:text-indigo-800">+ Add discipline</button>
            </div>

            <div>
                <label for="image_path" class="block text-sm font-medium text-gray-700 mb-1">Image path</label>
                <input type="text" id="image_path" name="image_path"
                       value="{{ old('image_path', $hero->image_path) }}"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                <p class="mt-1 text-xs text-gray-400">Plain path for now — real uploads come in Step 8.</p>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-indigo-600 text-white text-sm font-medium px-5 py-2 rounded-md hover:bg-indigo-700 transition">
                Save changes
            </button>
        </div>
    </form>
@endsection
