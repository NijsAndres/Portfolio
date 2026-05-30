@extends('admin.layouts.app')

@section('title', 'Education')

@section('content')
    <div class="flex justify-end mb-4">
        <a href="{{ route('admin.education.create') }}"
           class="bg-indigo-600 text-white text-sm font-medium px-4 py-2 rounded-md hover:bg-indigo-700 transition">
            + New entry
        </a>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-500 border-b border-gray-200">
                    <th class="px-6 py-3 font-medium">Institution</th>
                    <th class="px-6 py-3 font-medium">Degree</th>
                    <th class="px-6 py-3 font-medium">Period</th>
                    <th class="px-6 py-3 font-medium">Sort</th>
                    <th class="px-6 py-3 font-medium text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($education as $entry)
                    <tr>
                        <td class="px-6 py-3 font-medium text-gray-900">{{ $entry->institution }}</td>
                        <td class="px-6 py-3 text-gray-600">{{ $entry->degree ?? '—' }}</td>
                        <td class="px-6 py-3 text-gray-600">{{ $entry->period ?? '—' }}</td>
                        <td class="px-6 py-3 text-gray-600">{{ $entry->sort_order }}</td>
                        <td class="px-6 py-3">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('admin.education.edit', $entry) }}"
                                   class="text-indigo-600 hover:text-indigo-800 font-medium">Edit</a>
                                <form method="POST" action="{{ route('admin.education.destroy', $entry) }}"
                                      onsubmit="return confirm('Delete this entry?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-400">No education entries yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
