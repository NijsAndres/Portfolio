@extends('admin.layouts.app')

@php $exists = $education->exists; @endphp

@section('title', $exists ? 'Edit Education' : 'New Education')

@section('content')
    <form method="POST"
          action="{{ $exists ? route('admin.education.update', $education) : route('admin.education.store') }}"
          class="space-y-6">
        @csrf
        @if ($exists)
            @method('PUT')
        @endif

        <div class="card p-6 space-y-5">
            <div class="grid gap-x-5 gap-y-5 sm:grid-cols-2">
                <div>
                    <label for="institution" class="form-label">Institution <span class="text-brand-500">*</span></label>
                    <input type="text" id="institution" name="institution" required
                           value="{{ old('institution', $education->institution) }}"
                           class="form-input">
                </div>

                <div>
                    <label for="sort_order" class="form-label">Sort order</label>
                    <input type="number" id="sort_order" name="sort_order"
                           value="{{ old('sort_order', $education->sort_order) }}"
                           class="form-input">
                </div>
            </div>

            <x-admin.translatable-input name="degree" label="Degree" :model="$education" :max="255" />

            <x-admin.translatable-input name="period" label="Period" :model="$education" :max="255" placeholder="2021 – 2024" />
        </div>

        <div class="flex justify-between items-center">
            <a href="{{ route('admin.education.index') }}" class="btn-ghost">Cancel</a>
            <button type="submit" class="btn-primary">
                {{ $exists ? 'Save changes' : 'Create entry' }}
            </button>
        </div>
    </form>
@endsection
