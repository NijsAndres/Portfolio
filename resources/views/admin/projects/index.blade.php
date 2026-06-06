@extends('admin.layouts.app')

@section('title', 'Projects')

@section('content')
    {{-- Projects --}}
    <section class="mb-12">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-lg font-bold text-ink">Projects</h2>
                <p class="text-sm text-ink/50">Your portfolio work — shown as cards on the live site.</p>
            </div>
            <a href="{{ route('admin.projects.create') }}" class="btn-outline">+ New project</a>
        </div>

        <div class="card overflow-hidden">
            <div class="table-scroll">
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="w-10"></th>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Updated</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody data-reorder="{{ route('admin.projects.reorder') }}">
                    @forelse ($projects as $project)
                        <tr class="js-row-link" data-id="{{ $project->id }}" data-edit-url="{{ route('admin.projects.edit', $project) }}">
                            <td class="js-drag-handle text-center" title="Drag to reorder" aria-label="Drag to reorder">⠿</td>
                            <td class="cell-strong">
                                <a href="{{ route('admin.projects.edit', $project) }}" class="hover:text-brand-700 transition-colors">{{ $project->title }}</a>
                            </td>
                            <td>{{ $project->type ?? '—' }}</td>
                            <td>{{ $project->updated_at?->diffForHumans() ?? '—' }}</td>
                            <td class="js-row-actions">
                                <div class="flex items-center justify-end gap-4">
                                    <form method="POST" action="{{ route('admin.projects.duplicate', $project) }}">
                                        @csrf
                                        <button type="submit" class="font-semibold text-brand-700 hover:text-brand-800 transition-colors">Duplicate</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.projects.destroy', $project) }}"
                                          onsubmit="return confirm('Delete this project?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="font-semibold text-red-600 hover:text-red-700 transition-colors">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-ink/40">No projects yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </div>
    </section>

    {{-- Filters — managed here too. They change far less often than projects,
         so they sit at the bottom of the same page. --}}
    <section>
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-lg font-bold text-ink">Filters</h2>
                <p class="text-sm text-ink/50">Categories used to filter projects on the live site. Link them from a project's edit form.</p>
            </div>
            <a href="{{ route('admin.filters.create') }}" class="btn-outline">+ New filter</a>
        </div>

        <div class="card overflow-hidden">
            <div class="table-scroll">
            <table class="data-table">
                <thead>
                    <tr>
                        <th class="w-10"></th>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Projects</th>
                        <th>Updated</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody data-reorder="{{ route('admin.filters.reorder') }}">
                    @forelse ($filters as $filter)
                        <tr class="js-row-link" data-id="{{ $filter->id }}" data-edit-url="{{ route('admin.filters.edit', $filter) }}">
                            <td class="js-drag-handle text-center" title="Drag to reorder" aria-label="Drag to reorder">⠿</td>
                            <td class="cell-strong">
                                <a href="{{ route('admin.filters.edit', $filter) }}" class="hover:text-brand-700 transition-colors">{{ $filter->name }}</a>
                            </td>
                            <td>{{ $filter->slug }}</td>
                            <td>{{ $filter->projects_count }}</td>
                            <td>{{ $filter->updated_at?->diffForHumans() ?? '—' }}</td>
                            <td class="js-row-actions">
                                <div class="flex items-center justify-end gap-4">
                                    <form method="POST" action="{{ route('admin.filters.destroy', $filter) }}"
                                          onsubmit="return confirm('Delete this filter? Projects keep their data — only the links are removed.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="font-semibold text-red-600 hover:text-red-700 transition-colors">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-ink/40">No filters yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            </div>
        </div>
    </section>
@endsection
