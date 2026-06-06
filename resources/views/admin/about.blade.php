@extends('admin.layouts.app')

@section('title', 'About Section')

@section('content')
    <form method="POST" action="{{ route('admin.about.update') }}" class="max-w-4xl space-y-6">
        @csrf
        @method('PUT')

        <div class="card p-6 space-y-5">
            <div>
                <label for="bio_text" class="form-label">Bio</label>
                <div class="mb-2 flex items-center gap-3">
                    <button type="button" data-bold-target="bio_text_editor"
                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-ink/15 font-bold text-ink/70 hover:bg-ink/5 hover:text-ink transition-colors"
                            title="Bold (⌘/Ctrl+B)" aria-label="Toggle bold">B</button>
                </div>
                <div id="bio_text_editor" data-rich-field="bio_text" contenteditable="true"
                     class="form-input hidden border px-3 py-2 min-h-36 leading-relaxed focus:outline-none"></div>
                <textarea id="bio_text" name="bio_text" rows="6"
                          class="form-input">{{ old('bio_text', $about->bio_text) }}</textarea>
            </div>

            <div class="grid gap-x-5 gap-y-5 sm:grid-cols-2">
                <div>
                    <label for="born_in" class="form-label">Born in</label>
                    <input type="text" id="born_in" name="born_in"
                           value="{{ old('born_in', $about->born_in) }}"
                           class="form-input">
                </div>

                <div>
                    <label for="languages" class="form-label">Languages</label>
                    <input type="text" id="languages" name="languages"
                           value="{{ old('languages', $about->languages) }}"
                           class="form-input">
                </div>

                <div>
                    <label for="date_of_birth" class="form-label">Date of birth</label>
                    <input type="text" id="date_of_birth" name="date_of_birth"
                           value="{{ old('date_of_birth', $about->date_of_birth) }}"
                           placeholder="21/04/2003"
                           class="form-input">
                    <p class="mt-1 text-xs text-ink/40">Stored as free text (e.g. 21/04/2003).</p>
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="btn-primary">
                Save changes
            </button>
        </div>
    </form>
@endsection
