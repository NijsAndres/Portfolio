@extends('admin.layouts.app')

@php $exists = $project->exists; @endphp

@section('title', $exists ? 'Edit Project' : 'New Project')

@section('content')
    <form method="POST"
          action="{{ $exists ? route('admin.projects.update', $project) : route('admin.projects.store') }}"
          enctype="multipart/form-data"
          class="max-w-4xl space-y-6">
        @csrf
        @if ($exists)
            @method('PUT')
        @endif

        <div class="card p-6 space-y-5">
            <div>
                <label for="title" class="form-label">Title <span class="text-brand-500">*</span></label>
                <input type="text" id="title" name="title" required
                       value="{{ old('title', $project->title) }}"
                       class="form-input">
            </div>

            <div>
                <label for="description" class="form-label">Description</label>
                <textarea id="description" name="description" rows="3"
                          class="form-input">{{ old('description', $project->description) }}</textarea>
            </div>

            {{-- Tags repeater (tags[]) --}}
            <div x-data="{ items: @js(array_values(old('tags', $project->tags ?? []))) }">
                <label class="form-label">Tags</label>
                <template x-for="(item, idx) in items" :key="idx">
                    <div class="flex gap-2 mb-2">
                        <input type="text" name="tags[]" x-model="items[idx]"
                               class="form-input flex-1">
                        <button type="button" @click="items.splice(idx, 1)"
                                class="px-3 text-sm font-semibold text-red-600 hover:text-red-700">Remove</button>
                    </div>
                </template>
                <button type="button" @click="items.push('')"
                        class="mt-1 text-sm font-semibold text-brand-700 hover:text-brand-800">+ Add tag</button>
            </div>

            {{-- Filters (many-to-many) — separate from the free-text tags above. --}}
            @php $selectedFilters = old('filters', $project->filters->pluck('id')->all()); @endphp
            <div>
                <label class="form-label">Filters</label>
                @forelse ($filters as $filter)
                    <label class="flex items-center gap-2 mb-1.5 text-sm text-ink/80">
                        <input type="checkbox" name="filters[]" value="{{ $filter->id }}"
                               @checked(in_array($filter->id, $selectedFilters))
                               class="form-checkbox shadow-sm">
                        {{ $filter->name }}
                    </label>
                @empty
                    <p class="text-xs text-ink/40">
                        No filters yet — <a href="{{ route('admin.filters.create') }}" class="link-accent">create one</a> to link projects.
                    </p>
                @endforelse
            </div>

            <div class="grid gap-x-5 gap-y-5 sm:grid-cols-2">
                <div>
                    <label for="url" class="form-label">URL</label>
                    <input type="url" id="url" name="url"
                           value="{{ old('url', $project->url) }}"
                           placeholder="https://..."
                           class="form-input">
                </div>

                <div>
                    <label for="type" class="form-label">Type</label>
                    <input type="text" id="type" name="type"
                           value="{{ old('type', $project->type) }}"
                           class="form-input">
                </div>
            </div>

            <div>
                <label for="image" class="form-label">Image</label>
                @if ($exists && $project->image_url)
                    <img src="{{ $project->image_url }}" alt="Current image for {{ $project->title }}"
                         class="mb-2 h-32 w-auto rounded-lg border border-ink/10 object-cover">
                @endif
                <input type="file" id="image" name="image" accept="image/*"
                       class="w-full text-sm text-ink/70 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-brand-700 hover:file:bg-brand-100">
                <p class="mt-1 text-xs text-ink/40">JPG, PNG or WebP, up to 4 MB.@if ($exists) Leave empty to keep the current image.@endif</p>
            </div>

            <div>
                <label for="body" class="form-label">Body</label>
                <textarea id="body" name="body" rows="6"
                          class="form-input">{{ old('body', $project->body) }}</textarea>
            </div>

            <div>
                <label for="sort_order" class="form-label">Sort order</label>
                <input type="number" id="sort_order" name="sort_order"
                       value="{{ old('sort_order', $project->sort_order) }}"
                       class="form-input w-32">
            </div>
        </div>

        <div class="flex justify-between items-center">
            <a href="{{ route('admin.projects.index') }}" class="btn-ghost">Cancel</a>
            <button type="submit" class="btn-primary">
                {{ $exists ? 'Save changes' : 'Create project' }}
            </button>
        </div>
    </form>
@endsection
