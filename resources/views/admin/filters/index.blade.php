@extends('admin.layouts.app')

@section('title', 'Filters')

@section('content')
    <div class="flex justify-end mb-4">
        <a href="{{ route('admin.filters.create') }}"
           class="bg-indigo-600 text-white text-sm font-medium px-4 py-2 rounded-md hover:bg-indigo-700 transition">
            + New filter
        </a>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-500 border-b border-gray-200">
                    <th class="px-6 py-3 font-medium">Name</th>
                    <th class="px-6 py-3 font-medium">Slug</th>
                    <th class="px-6 py-3 font-medium">Projects</th>
                    <th class="px-6 py-3 font-medium">Sort</th>
                    <th class="px-6 py-3 font-medium">Updated</th>
                    <th class="px-6 py-3 font-medium text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($filters as $filter)
                    <tr>
                        <td class="px-6 py-3 font-medium text-gray-900">{{ $filter->name }}</td>
                        <td class="px-6 py-3 text-gray-600">{{ $filter->slug }}</td>
                        <td class="px-6 py-3 text-gray-600">{{ $filter->projects_count }}</td>
                        <td class="px-6 py-3 text-gray-600">{{ $filter->sort_order }}</td>
                        <td class="px-6 py-3 text-gray-500">{{ $filter->updated_at?->diffForHumans() ?? '—' }}</td>
                        <td class="px-6 py-3">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('admin.filters.edit', $filter) }}"
                                   class="text-indigo-600 hover:text-indigo-800 font-medium">Edit</a>
                                <form method="POST" action="{{ route('admin.filters.destroy', $filter) }}"
                                      onsubmit="return confirm('Delete this filter? Projects keep their data — only the links are removed.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 font-medium">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-400">No filters yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
