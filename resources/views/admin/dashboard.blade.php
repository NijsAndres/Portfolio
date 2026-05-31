@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
    @php $cvPath = \App\Models\SiteSetting::get('cv_path'); @endphp

    {{-- Stat cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
        @foreach (['projects' => 'Projects', 'education' => 'Education', 'experience' => 'Experience'] as $key => $label)
            <div class="card-stat">
                <p class="text-sm font-semibold uppercase tracking-wide text-ink/50">{{ $label }}</p>
                <p class="mt-2 text-4xl font-bold text-ink">{{ $stats[$key] ?? 0 }}</p>
            </div>
        @endforeach
    </div>

    {{-- Singletons overview --}}
    <div class="card mb-8 overflow-hidden">
        <div class="card-header">
            <h2 class="card-title">Site content</h2>
        </div>
        <table class="data-table">
            <thead>
                <tr>
                    <th>Section</th>
                    <th>Status</th>
                    <th>Last updated</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @php
                    $singletons = [
                        ['label' => 'Hero',    'model' => $hero,    'route' => 'admin.hero.edit'],
                        ['label' => 'About',   'model' => $about,   'route' => 'admin.about.edit'],
                        ['label' => 'Contact', 'model' => $contact, 'route' => 'admin.contact.edit'],
                    ];
                @endphp
                @foreach ($singletons as $row)
                    <tr>
                        <td class="cell-strong">{{ $row['label'] }}</td>
                        <td>
                            @if ($row['model'])
                                <span class="badge-accent">Configured</span>
                            @else
                                <span class="badge-muted">Not set</span>
                            @endif
                        </td>
                        <td>
                            {{ $row['model']?->updated_at?->diffForHumans() ?? '—' }}
                        </td>
                        <td class="text-right">
                            <a href="{{ route($row['route']) }}" class="link-accent">Edit</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- CV upload (no dedicated GET route; placed here per Step 6 decision) --}}
    <div id="cv-upload" class="card">
        <div class="card-header">
            <h2 class="card-title">CV / Résumé</h2>
        </div>
        <div class="p-6">
            <p class="text-sm text-ink/60 mb-4">
                Current file:
                @if ($cvPath)
                    <span class="font-semibold text-ink">{{ $cvPath }}</span>
                @else
                    <span class="text-ink/40">none uploaded yet</span>
                @endif
            </p>

            <form method="POST" action="{{ route('admin.cv.update') }}" enctype="multipart/form-data" class="flex items-center gap-4">
                @csrf
                <input type="file" name="cv" accept=".pdf"
                       class="block text-sm text-ink/70 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-brand-50 file:text-brand-700 hover:file:bg-brand-100" />
                <button type="submit" class="btn-primary">Upload CV</button>
            </form>
            <p class="mt-3 text-xs text-ink/40">PDF only, max 10&nbsp;MB. File storage is wired up in Step 8.</p>
        </div>
    </div>
@endsection
