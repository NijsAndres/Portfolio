@extends('admin.layouts.app')

@section('title', 'Contact Details')

@section('content')
    <form method="POST" action="{{ route('admin.contact.update') }}" class="max-w-2xl space-y-6">
        @csrf
        @method('PUT')

        <div class="bg-white rounded-lg border border-gray-200 p-6 space-y-5">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" id="email" name="email"
                       value="{{ old('email', $contact->email) }}"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>

            <div>
                <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                <input type="text" id="phone" name="phone"
                       value="{{ old('phone', $contact->phone) }}"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>

            <div>
                <label for="linkedin_url" class="block text-sm font-medium text-gray-700 mb-1">LinkedIn URL</label>
                <input type="url" id="linkedin_url" name="linkedin_url"
                       value="{{ old('linkedin_url', $contact->linkedin_url) }}"
                       placeholder="https://linkedin.com/in/..."
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>

            <div>
                <label for="github_url" class="block text-sm font-medium text-gray-700 mb-1">GitHub URL</label>
                <input type="url" id="github_url" name="github_url"
                       value="{{ old('github_url', $contact->github_url) }}"
                       placeholder="https://github.com/..."
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>

            <div>
                <label for="intro_text" class="block text-sm font-medium text-gray-700 mb-1">Intro text</label>
                <textarea id="intro_text" name="intro_text" rows="4"
                          class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">{{ old('intro_text', $contact->intro_text) }}</textarea>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit" class="bg-indigo-600 text-white text-sm font-medium px-5 py-2 rounded-md hover:bg-indigo-700 transition">
                Save changes
            </button>
        </div>
    </form>
@endsection
