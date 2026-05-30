@extends('admin.layouts.app')

@php $exists = $project->exists; @endphp

@section('title', $exists ? 'Edit Project' : 'New Project')

@section('content')
    <form method="POST"
          action="{{ $exists ? route('admin.projects.update', $project) : route('admin.projects.store') }}"
          enctype="multipart/form-data"
          class="max-w-2xl space-y-6">
        @csrf
        @if ($exists)
            @method('PUT')
        @endif

        <div class="bg-white rounded-lg border border-gray-200 p-6 space-y-5">
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Title <span class="text-red-500">*</span></label>
                <input type="text" id="title" name="title" required
                       value="{{ old('title', $project->title) }}"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea id="description" name="description" rows="3"
                          class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">{{ old('description', $project->description) }}</textarea>
            </div>

            {{-- Tags repeater (tags[]) --}}
            <div x-data="{ items: @js(array_values(old('tags', $project->tags ?? []))) }">
                <label class="block text-sm font-medium text-gray-700 mb-1">Tags</label>
                <template x-for="(item, idx) in items" :key="idx">
                    <div class="flex gap-2 mb-2">
                        <input type="text" name="tags[]" x-model="items[idx]"
                               class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                        <button type="button" @click="items.splice(idx, 1)"
                                class="px-3 text-sm text-red-600 hover:text-red-800">Remove</button>
                    </div>
                </template>
                <button type="button" @click="items.push('')"
                        class="mt-1 text-sm font-medium text-indigo-600 hover:text-indigo-800">+ Add tag</button>
            </div>

            {{-- Filters (many-to-many) — separate from the free-text tags above. --}}
            @php $selectedFilters = old('filters', $project->filters->pluck('id')->all()); @endphp
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Filters</label>
                @forelse ($filters as $filter)
                    <label class="flex items-center gap-2 mb-1.5 text-sm text-gray-700">
                        <input type="checkbox" name="filters[]" value="{{ $filter->id }}"
                               @checked(in_array($filter->id, $selectedFilters))
                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        {{ $filter->name }}
                    </label>
                @empty
                    <p class="text-xs text-gray-400">
                        No filters yet — <a href="{{ route('admin.filters.index') }}" class="text-indigo-600 hover:text-indigo-800">create one</a> to link projects.
                    </p>
                @endforelse
            </div>

            <div>
                <label for="url" class="block text-sm font-medium text-gray-700 mb-1">URL</label>
                <input type="url" id="url" name="url"
                       value="{{ old('url', $project->url) }}"
                       placeholder="https://..."
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>

            <div>
                <label for="image" class="block text-sm font-medium text-gray-700 mb-1">Image</label>
                @if ($exists && $project->image_url)
                    <img src="{{ $project->image_url }}" alt="Current image for {{ $project->title }}"
                         class="mb-2 h-32 w-auto rounded-md border border-gray-200 object-cover">
                @endif
                <input type="file" id="image" name="image" accept="image/*"
                       class="w-full text-sm text-gray-700 file:mr-3 file:rounded-md file:border-0 file:bg-indigo-50 file:px-3 file:py-2 file:text-sm file:font-medium file:text-indigo-700 hover:file:bg-indigo-100">
                <p class="mt-1 text-xs text-gray-400">JPG, PNG or WebP, up to 4 MB.@if ($exists) Leave empty to keep the current image.@endif</p>
            </div>

            <div>
                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                <input type="text" id="type" name="type"
                       value="{{ old('type', $project->type) }}"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>

            <div>
                <label for="body" class="block text-sm font-medium text-gray-700 mb-1">Body</label>
                <textarea id="body" name="body" rows="6"
                          class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">{{ old('body', $project->body) }}</textarea>
            </div>

            <div>
                <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1">Sort order</label>
                <input type="number" id="sort_order" name="sort_order"
                       value="{{ old('sort_order', $project->sort_order) }}"
                       class="w-32 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
        </div>

        <div class="flex justify-between items-center">
            <a href="{{ route('admin.projects.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700">Cancel</a>
            <button type="submit" class="bg-indigo-600 text-white text-sm font-medium px-5 py-2 rounded-md hover:bg-indigo-700 transition">
                {{ $exists ? 'Save changes' : 'Create project' }}
            </button>
        </div>
    </form>
@endsection
