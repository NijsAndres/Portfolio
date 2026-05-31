@extends('admin.layouts.app')

@php $exists = $experience->exists; @endphp

@section('title', $exists ? 'Edit Experience' : 'New Experience')

@section('content')
    <form method="POST"
          action="{{ $exists ? route('admin.experience.update', $experience) : route('admin.experience.store') }}"
          class="max-w-4xl space-y-6">
        @csrf
        @if ($exists)
            @method('PUT')
        @endif

        <div class="card p-6 space-y-5">
            <div>
                <label for="company" class="form-label">Company <span class="text-brand-500">*</span></label>
                <input type="text" id="company" name="company" required
                       value="{{ old('company', $experience->company) }}"
                       class="form-input">
            </div>

            <div class="grid gap-x-5 gap-y-5 sm:grid-cols-2">
                <div>
                    <label for="role" class="form-label">Role</label>
                    <input type="text" id="role" name="role"
                           value="{{ old('role', $experience->role) }}"
                           class="form-input">
                </div>

                <div>
                    <label for="period" class="form-label">Period</label>
                    <input type="text" id="period" name="period"
                           value="{{ old('period', $experience->period) }}"
                           placeholder="2023 – present"
                           class="form-input">
                </div>
            </div>

            <div>
                <label for="sort_order" class="form-label">Sort order</label>
                <input type="number" id="sort_order" name="sort_order"
                       value="{{ old('sort_order', $experience->sort_order) }}"
                       class="form-input w-32">
            </div>
        </div>

        <div class="flex justify-between items-center">
            <a href="{{ route('admin.experience.index') }}" class="btn-ghost">Cancel</a>
            <button type="submit" class="btn-primary">
                {{ $exists ? 'Save changes' : 'Create entry' }}
            </button>
        </div>
    </form>
@endsection
