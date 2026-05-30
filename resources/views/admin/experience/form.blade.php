@extends('admin.layouts.app')

@php $exists = $experience->exists; @endphp

@section('title', $exists ? 'Edit Experience' : 'New Experience')

@section('content')
    <form method="POST"
          action="{{ $exists ? route('admin.experience.update', $experience) : route('admin.experience.store') }}"
          class="max-w-2xl space-y-6">
        @csrf
        @if ($exists)
            @method('PUT')
        @endif

        <div class="bg-white rounded-lg border border-gray-200 p-6 space-y-5">
            <div>
                <label for="company" class="block text-sm font-medium text-gray-700 mb-1">Company <span class="text-red-500">*</span></label>
                <input type="text" id="company" name="company" required
                       value="{{ old('company', $experience->company) }}"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>

            <div>
                <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Role</label>
                <input type="text" id="role" name="role"
                       value="{{ old('role', $experience->role) }}"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>

            <div>
                <label for="period" class="block text-sm font-medium text-gray-700 mb-1">Period</label>
                <input type="text" id="period" name="period"
                       value="{{ old('period', $experience->period) }}"
                       placeholder="2023 – present"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>

            <div>
                <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1">Sort order</label>
                <input type="number" id="sort_order" name="sort_order"
                       value="{{ old('sort_order', $experience->sort_order) }}"
                       class="w-32 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
        </div>

        <div class="flex justify-between items-center">
            <a href="{{ route('admin.experience.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700">Cancel</a>
            <button type="submit" class="bg-indigo-600 text-white text-sm font-medium px-5 py-2 rounded-md hover:bg-indigo-700 transition">
                {{ $exists ? 'Save changes' : 'Create entry' }}
            </button>
        </div>
    </form>
@endsection
