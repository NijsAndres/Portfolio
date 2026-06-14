@extends('admin.layouts.app')

@section('title', 'Contact Details')

@section('content')
    <form method="POST" action="{{ route('admin.contact.update') }}" class="space-y-6">
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

            <x-admin.translatable-textarea name="intro_text" label="Intro text" :model="$contact" :rows="4" :rich="true" />
        </div>

        <div class="flex justify-end">
            <button type="submit" class="btn-primary">
                Save changes
            </button>
        </div>
    </form>
@endsection
