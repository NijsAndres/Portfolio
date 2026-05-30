@extends('admin.layouts.app')

@php $exists = $filter->exists; @endphp

@section('title', $exists ? 'Edit Filter' : 'New Filter')

@section('content')
    <form method="POST"
          action="{{ $exists ? route('admin.filters.update', $filter) : route('admin.filters.store') }}"
          class="max-w-2xl space-y-6">
        @csrf
        @if ($exists)
            @method('PUT')
        @endif

        <div class="bg-white rounded-lg border border-gray-200 p-6 space-y-5">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name <span class="text-red-500">*</span></label>
                <input type="text" id="name" name="name" required
                       value="{{ old('name', $filter->name) }}"
                       class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                <p class="mt-1 text-xs text-gray-400">The slug (used as the filter key) is generated automatically from the name.</p>
            </div>

            <div>
                <label for="sort_order" class="block text-sm font-medium text-gray-700 mb-1">Sort order</label>
                <input type="number" id="sort_order" name="sort_order"
                       value="{{ old('sort_order', $filter->sort_order) }}"
                       class="w-32 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
            </div>
        </div>

        <div class="flex justify-between items-center">
            <a href="{{ route('admin.filters.index') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700">Cancel</a>
            <button type="submit" class="bg-indigo-600 text-white text-sm font-medium px-5 py-2 rounded-md hover:bg-indigo-700 transition">
                {{ $exists ? 'Save changes' : 'Create filter' }}
            </button>
        </div>
    </form>
@endsection
