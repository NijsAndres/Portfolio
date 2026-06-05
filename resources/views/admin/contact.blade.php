@extends('admin.layouts.app')

@section('title', 'Contact Details')

@section('content')
    <form method="POST" action="{{ route('admin.contact.update') }}" class="max-w-4xl space-y-6">
        @csrf
        @method('PUT')

        <div class="card p-6 space-y-5">
            <div class="grid gap-x-5 gap-y-5 sm:grid-cols-2">
                <div>
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email"
                           value="{{ old('email', $contact->email) }}"
                           class="form-input">
                </div>

                <div>
                    <label for="phone" class="form-label">Phone</label>
                    <input type="text" id="phone" name="phone"
                           value="{{ old('phone', $contact->phone) }}"
                           class="form-input">
                </div>

                <div>
                    <label for="linkedin_url" class="form-label">LinkedIn URL</label>
                    <input type="url" id="linkedin_url" name="linkedin_url"
                           value="{{ old('linkedin_url', $contact->linkedin_url) }}"
                           placeholder="https://linkedin.com/in/..."
                           class="form-input">
                </div>

                <div>
                    <label for="github_url" class="form-label">GitHub URL</label>
                    <input type="url" id="github_url" name="github_url"
                           value="{{ old('github_url', $contact->github_url) }}"
                           placeholder="https://github.com/..."
                           class="form-input">
                </div>
            </div>

            <div>
                <label for="intro_text" class="form-label">Intro text</label>
                <div class="mb-2 flex items-center gap-3">
                    <button type="button" data-bold-target="intro_text_editor"
                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg border border-ink/15 font-bold text-ink/70 hover:bg-ink/5 hover:text-ink transition-colors"
                            title="Bold (⌘/Ctrl+B)" aria-label="Toggle bold">B</button>
                    <p class="text-xs text-ink/40">Select text and click <strong>B</strong> (or ⌘/Ctrl+B) to toggle bold.</p>
                </div>
                <div id="intro_text_editor" data-rich-field="intro_text" contenteditable="true"
                     class="form-input hidden border px-3 py-2 min-h-24 leading-relaxed focus:outline-none"></div>
                <textarea id="intro_text" name="intro_text" rows="4"
                          class="form-input">{{ old('intro_text', $contact->intro_text) }}</textarea>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="btn-primary">
                Save changes
            </button>
        </div>
    </form>
@endsection
