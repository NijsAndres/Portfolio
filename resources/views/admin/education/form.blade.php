@extends('admin.layouts.app')

@php $exists = $education->exists; @endphp

@section('title', $exists ? 'Edit Education' : 'New Education')

@section('content')
    <form method="POST"
          action="{{ $exists ? route('admin.education.update', $education) : route('admin.education.store') }}"
          class="max-w-2xl space-y-6">
        @csrf
        @if ($exists)
            @method('PUT')
        @endif

        <div class="card p-6 space-y-5">
            <div>
                <label for="institution" class="form-label">Institution <span class="text-brand-500">*</span></label>
                <input type="text" id="institution" name="institution" required
                       value="{{ old('institution', $education->institution) }}"
                       class="form-input">
            </div>

            <div>
                <label for="degree" class="form-label">Degree</label>
                <input type="text" id="degree" name="degree"
                       value="{{ old('degree', $education->degree) }}"
                       class="form-input">
            </div>

            <div>
                <label for="period" class="form-label">Period</label>
                <input type="text" id="period" name="period"
                       value="{{ old('period', $education->period) }}"
                       placeholder="2021 – 2024"
                       class="form-input">
            </div>

            <div>
                <label for="sort_order" class="form-label">Sort order</label>
                <input type="number" id="sort_order" name="sort_order"
                       value="{{ old('sort_order', $education->sort_order) }}"
                       class="form-input w-32">
            </div>
        </div>

        <div class="flex justify-between items-center">
            <a href="{{ route('admin.education.index') }}" class="btn-ghost">Cancel</a>
            <button type="submit" class="btn-primary">
                {{ $exists ? 'Save changes' : 'Create entry' }}
            </button>
        </div>
    </form>
@endsection
