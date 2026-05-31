@extends('admin.layouts.app')

@section('title', 'Experience')

@section('content')
    <div class="flex justify-end mb-4">
        <a href="{{ route('admin.experience.create') }}" class="btn-outline">+ New entry</a>
    </div>

    <div class="card overflow-hidden">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Company</th>
                    <th>Role</th>
                    <th>Period</th>
                    <th>Sort</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($experience as $entry)
                    <tr>
                        <td class="cell-strong">{{ $entry->company }}</td>
                        <td>{{ $entry->role ?? '—' }}</td>
                        <td>{{ $entry->period ?? '—' }}</td>
                        <td>{{ $entry->sort_order }}</td>
                        <td>
                            <div class="flex items-center justify-end gap-4">
                                <a href="{{ route('admin.experience.edit', $entry) }}" class="link-accent">Edit</a>
                                <form method="POST" action="{{ route('admin.experience.destroy', $entry) }}"
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
                        <td colspan="5" class="px-6 py-8 text-center text-ink/40">No experience entries yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
