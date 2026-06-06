@extends('admin.layouts.app')

@section('title', 'Media')

@php
    // Flatten media for Alpine: the grid renders from this, and the detail panel
    // reads the selected item. update_url/destroy_url are baked in so the modal
    // forms don't need to build routes client-side.
    $mediaItems = $media->map(fn ($m) => [
        'id' => $m->id,
        'url' => $m->url,
        'name' => $m->title ?: $m->original_name,
        'original_name' => $m->original_name,
        'mime' => $m->mime_type,
        'size' => $m->human_size,
        'width' => $m->width,
        'height' => $m->height,
        'created' => $m->created_at?->format('M j, Y'),
        'alt' => $m->alt ?? '',
        'title' => $m->title ?? '',
        'caption' => $m->caption ?? '',
        'description' => $m->description ?? '',
        'update_url' => route('admin.media.update', $m),
        'destroy_url' => route('admin.media.destroy', $m),
    ])->values();
@endphp

@section('content')
    <style>[x-cloak]{display:none!important;}</style>

    <div x-data="{ items: @js($mediaItems), selected: null }">
        {{-- Upload zone --}}
        <form method="POST" action="{{ route('admin.media.store') }}" enctype="multipart/form-data"
              x-ref="uploadForm"
              x-data="{ over: false }"
              @dragover.prevent="over = true" @dragleave.prevent="over = false"
              @drop.prevent="over = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.uploadForm.submit()"
              class="mb-6">
            @csrf
            <label :class="over ? 'border-brand-500 bg-brand-50' : 'border-ink/20 bg-white'"
                   class="flex cursor-pointer flex-col items-center justify-center gap-1 rounded-lg border-2 border-dashed px-6 py-10 text-center transition-colors hover:border-brand-500">
                <span class="text-sm font-semibold text-ink">Drop images here or click to upload</span>
                <span class="text-xs text-ink/40">JPG, PNG, WebP or GIF, up to 8 MB each. Multiple files allowed.</span>
                <input type="file" name="files[]" multiple accept="image/*" x-ref="fileInput"
                       @change="$refs.uploadForm.submit()" class="hidden">
            </label>
        </form>

        {{-- Grid --}}
        <template x-if="items.length === 0">
            <div class="card p-10 text-center text-ink/40">No media yet — upload your first image above.</div>
        </template>

        <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5">
            <template x-for="item in items" :key="item.id">
                <button type="button" @click="selected = item"
                        class="card group relative aspect-square overflow-hidden p-0 transition-all hover:-translate-y-0.5 hover:shadow-card-hover hover:border-brand-500">
                    <img :src="item.url" :alt="item.alt || item.original_name"
                         class="h-full w-full object-cover">
                    <span class="absolute inset-x-0 bottom-0 truncate bg-ink/70 px-2 py-1 text-left text-xs font-medium text-white opacity-0 transition-opacity group-hover:opacity-100"
                          x-text="item.name"></span>
                </button>
            </template>
        </div>

        {{-- Detail panel --}}
        <div x-cloak x-show="selected" @keydown.escape.window="selected = null"
             class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-ink/50" @click="selected = null"></div>

            <div x-show="selected" x-transition
                 class="card relative z-10 flex max-h-[90vh] w-full max-w-3xl flex-col overflow-hidden md:flex-row">
                {{-- Preview --}}
                <div class="flex items-center justify-center bg-cream/60 p-6 md:w-1/2">
                    <img :src="selected?.url" :alt="selected?.alt || selected?.original_name"
                         class="max-h-[70vh] w-auto rounded-lg object-contain">
                </div>

                {{-- Meta + edit --}}
                <div class="flex flex-1 flex-col overflow-y-auto p-6">
                    <div class="mb-4 flex items-start justify-between gap-3">
                        <h2 class="font-bold text-ink" x-text="selected?.original_name"></h2>
                        <button type="button" @click="selected = null"
                                class="text-ink/40 hover:text-ink" aria-label="Close">&times;</button>
                    </div>

                    {{-- Read-only metadata --}}
                    <dl class="mb-4 space-y-1 text-xs text-ink/60">
                        <div class="flex justify-between gap-4"><dt>Type</dt><dd x-text="selected?.mime || '—'"></dd></div>
                        <div class="flex justify-between gap-4"><dt>Size</dt><dd x-text="selected?.size"></dd></div>
                        <div class="flex justify-between gap-4"><dt>Dimensions</dt>
                            <dd x-text="selected?.width ? `${selected.width} × ${selected.height}` : '—'"></dd></div>
                        <div class="flex justify-between gap-4"><dt>Uploaded</dt><dd x-text="selected?.created || '—'"></dd></div>
                    </dl>

                    {{-- Copyable URL --}}
                    <div class="mb-4" x-data="{ copied: false }">
                        <label class="form-label">File URL</label>
                        <div class="flex gap-2">
                            <input type="text" readonly :value="selected?.url"
                                   class="form-input flex-1 text-xs" @focus="$event.target.select()">
                            <button type="button" class="btn-outline shrink-0 px-3"
                                    @click="navigator.clipboard.writeText(selected.url); copied = true; setTimeout(() => copied = false, 1500)"
                                    x-text="copied ? 'Copied' : 'Copy'"></button>
                        </div>
                    </div>

                    {{-- Editable metadata --}}
                    <form method="POST" :action="selected?.update_url" class="space-y-3">
                        @csrf
                        @method('PUT')
                        <div>
                            <label class="form-label">Alt text</label>
                            <input type="text" name="alt" x-model="selected.alt" class="form-input">
                        </div>
                        <div>
                            <label class="form-label">Title</label>
                            <input type="text" name="title" x-model="selected.title" class="form-input">
                        </div>
                        <div>
                            <label class="form-label">Caption</label>
                            <input type="text" name="caption" x-model="selected.caption" class="form-input">
                        </div>
                        <div>
                            <label class="form-label">Description</label>
                            <textarea name="description" rows="3" x-model="selected.description" class="form-input"></textarea>
                        </div>

                        <div class="flex items-center justify-between pt-2">
                            <button type="submit" class="btn-primary">Save</button>
                        </div>
                    </form>

                    {{-- Delete --}}
                    <form method="POST" :action="selected?.destroy_url" class="mt-3 border-t border-ink/10 pt-3"
                          @submit="return confirm('Delete this image? Anything using it will lose its image.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="font-semibold text-red-600 hover:text-red-700">Delete permanently</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
