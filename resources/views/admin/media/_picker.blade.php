{{--
    Reusable media picker. Renders a hidden `media_id` input plus a preview and a
    "Select image" button that opens a modal grid of the library. Selecting sets
    the hidden input; "Upload new" inside the modal AJAX-uploads a single file,
    prepends it to the grid and auto-selects it.

    Expects:
      $selected — currently selected media id (or null)
      $media    — collection of all Media to choose from
--}}
@php
    $pickerItems = $media->map(fn ($m) => [
        'id' => $m->id,
        'url' => $m->url,
        'name' => $m->title ?: $m->original_name,
        'alt' => $m->alt ?? '',
    ])->values();
@endphp

<style>[x-cloak]{display:none!important;}</style>

<div x-data="{
        open: false,
        items: @js($pickerItems),
        selectedId: @js($selected ? (int) $selected : null),
        get current() { return this.items.find(i => i.id === this.selectedId) || null; },
        select(id) { this.selectedId = id; this.open = false; },
        async upload(event) {
            const file = event.target.files[0];
            if (! file) return;
            const data = new FormData();
            data.append('file', file);
            const res = await fetch('{{ route('admin.media.store') }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'Accept': 'application/json',
                },
                body: data,
            });
            const json = await res.json();
            const m = json.media[0];
            this.items.unshift({ id: m.id, url: m.url, name: m.title, alt: m.alt || '' });
            this.selectedId = m.id;
            this.open = false;
            event.target.value = '';
        },
     }">
    <label class="form-label">Image</label>
    <input type="hidden" name="media_id" :value="selectedId">

    <div class="flex items-center gap-4">
        <template x-if="current">
            <img :src="current.url" :alt="current.alt || current.name"
                 class="h-24 w-24 rounded-lg border border-ink/10 object-cover">
        </template>
        <template x-if="! current">
            <div class="flex h-24 w-24 items-center justify-center rounded-lg border border-dashed border-ink/20 text-xs text-ink/40">No image</div>
        </template>

        <div class="flex gap-3">
            <button type="button" @click="open = true" class="btn-outline">Select image</button>
            <button type="button" x-show="current" @click="selectedId = null" class="btn-ghost">Remove</button>
        </div>
    </div>

    {{-- Picker modal --}}
    <div x-cloak x-show="open" @keydown.escape.window="open = false"
         class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-ink/50" @click="open = false"></div>

        <div x-show="open" x-transition
             class="card relative z-10 flex max-h-[85vh] w-full max-w-3xl flex-col overflow-hidden">
            <div class="card-header flex items-center justify-between">
                <h2 class="card-title">Select an image</h2>
                <label class="btn-outline cursor-pointer">
                    Upload new
                    <input type="file" accept="image/*" class="hidden" @change="upload($event)">
                </label>
            </div>

            <div class="flex-1 overflow-y-auto p-6">
                <template x-if="items.length === 0">
                    <p class="text-center text-sm text-ink/40">No media yet — use "Upload new" above.</p>
                </template>

                <div class="grid grid-cols-3 gap-3 sm:grid-cols-4">
                    <template x-for="item in items" :key="item.id">
                        <button type="button" @click="select(item.id)"
                                :class="item.id === selectedId ? 'border-brand-500 ring-2 ring-brand-500' : 'border-ink/10'"
                                class="relative aspect-square overflow-hidden rounded-lg border bg-cream/60 transition-all hover:border-brand-500">
                            <img :src="item.url" :alt="item.alt || item.name" class="h-full w-full object-cover">
                        </button>
                    </template>
                </div>
            </div>

            <div class="card-header flex justify-end border-t border-b-0">
                <button type="button" @click="open = false" class="btn-ghost">Done</button>
            </div>
        </div>
    </div>
</div>
