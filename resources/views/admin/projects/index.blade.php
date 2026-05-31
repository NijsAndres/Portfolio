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
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Sort</th>
                        <th>Updated</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($projects as $project)
                        <tr>
                            <td class="cell-strong">{{ $project->title }}</td>
                            <td>{{ $project->type ?? '—' }}</td>
                            <td>{{ $project->sort_order }}</td>
                            <td>{{ $project->updated_at?->diffForHumans() ?? '—' }}</td>
                            <td>
                                <div class="flex items-center justify-end gap-4">
                                    <a href="{{ route('admin.projects.edit', $project) }}" class="link-accent">Edit</a>
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
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Projects</th>
                        <th>Sort</th>
                        <th>Updated</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($filters as $filter)
                        <tr>
                            <td class="cell-strong">{{ $filter->name }}</td>
                            <td>{{ $filter->slug }}</td>
                            <td>{{ $filter->projects_count }}</td>
                            <td>{{ $filter->sort_order }}</td>
                            <td>{{ $filter->updated_at?->diffForHumans() ?? '—' }}</td>
                            <td>
                                <div class="flex items-center justify-end gap-4">
                                    <a href="{{ route('admin.filters.edit', $filter) }}" class="link-accent">Edit</a>
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
    </section>
@endsection
