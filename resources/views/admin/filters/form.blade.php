@extends('admin.layouts.app')

@php $exists = $filter->exists; @endphp

@section('title', $exists ? 'Edit Filter' : 'New Filter')

@section('content')
    <form method="POST"
          action="{{ $exists ? route('admin.filters.update', $filter) : route('admin.filters.store') }}"
          class="space-y-6">
        @csrf
        @if ($exists)
            @method('PUT')
        @endif

        <div class="card p-6 space-y-5">
            <div class="grid gap-x-5 gap-y-5 sm:grid-cols-2">
                <div>
                    <x-admin.translatable-input name="name" label="Name" :model="$filter" :required="true" :max="50" />
                    <p class="mt-1 text-xs text-ink/40">The slug (used as the filter key) is generated automatically from the English name.</p>
                </div>

                <div>
                    <label for="sort_order" class="form-label">Sort order</label>
                    <input type="number" id="sort_order" name="sort_order"
                           value="{{ old('sort_order', $filter->sort_order) }}"
                           class="form-input">
                </div>
            </div>
        </div>

        <div class="flex justify-between items-center">
            <a href="{{ route('admin.projects.index') }}" class="btn-ghost">Cancel</a>
            <button type="submit" class="btn-primary">
                {{ $exists ? 'Save changes' : 'Create filter' }}
            </button>
        </div>
    </form>
@endsection
