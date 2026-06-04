@extends('admin.layouts.app')

@section('title', 'Education')

@section('content')
    <div class="flex justify-end mb-4">
        <a href="{{ route('admin.education.create') }}" class="btn-outline">+ New entry</a>
    </div>

    <div class="card overflow-hidden">
        <table class="data-table">
            <thead>
                <tr>
                    <th class="w-10"></th>
                    <th>Institution</th>
                    <th>Degree</th>
                    <th>Period</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody data-reorder="{{ route('admin.education.reorder') }}">
                @forelse ($education as $entry)
                    <tr class="js-row-link" data-id="{{ $entry->id }}" data-edit-url="{{ route('admin.education.edit', $entry) }}">
                        <td class="js-drag-handle text-center" title="Drag to reorder" aria-label="Drag to reorder">⠿</td>
                        <td class="cell-strong">
                            <a href="{{ route('admin.education.edit', $entry) }}" class="hover:text-brand-700 transition-colors">{{ $entry->institution }}</a>
                        </td>
                        <td>{{ $entry->degree ?? '—' }}</td>
                        <td>{{ $entry->period ?? '—' }}</td>
                        <td class="js-row-actions">
                            <div class="flex items-center justify-end gap-4">
                                <form method="POST" action="{{ route('admin.education.destroy', $entry) }}"
                                      onsubmit="return confirm('Delete this entry?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="font-semibold text-red-600 hover:text-red-700 transition-colors">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-ink/40">No education entries yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
