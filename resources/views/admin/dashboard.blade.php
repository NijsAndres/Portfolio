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

    {{-- Analytics (Step 11) --}}
    @php
        $eventLabels = [
            'page_view' => 'Page view',
            'cv_download' => 'CV download',
            'project_click' => 'Project click',
            'contact_email' => 'Email click',
            'contact_linkedin' => 'LinkedIn click',
            'contact_github' => 'GitHub click',
        ];
    @endphp

    <h2 class="text-lg font-bold text-ink mb-4">Analytics</h2>

    {{-- Headline numbers --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="card-stat">
            <p class="text-sm font-semibold uppercase tracking-wide text-ink/50">Page views</p>
            <p class="mt-2 text-4xl font-bold text-ink">{{ number_format($analytics['viewsTotal']) }}</p>
            <p class="mt-1 text-xs text-ink/40">{{ number_format($analytics['viewsThisMonth']) }} this month</p>
        </div>
        <div class="card-stat">
            <p class="text-sm font-semibold uppercase tracking-wide text-ink/50">CV downloads</p>
            <p class="mt-2 text-4xl font-bold text-ink">{{ number_format($analytics['cvTotal']) }}</p>
            <p class="mt-1 text-xs text-ink/40">{{ number_format($analytics['cvThisMonth']) }} this month</p>
        </div>
        <div class="card-stat">
            <p class="text-sm font-semibold uppercase tracking-wide text-ink/50">Email clicks</p>
            <p class="mt-2 text-4xl font-bold text-ink">{{ number_format($analytics['contactBreakdown']['email']) }}</p>
        </div>
        <div class="card-stat">
            <p class="text-sm font-semibold uppercase tracking-wide text-ink/50">Social clicks</p>
            <p class="mt-2 text-4xl font-bold text-ink">{{ number_format($analytics['contactBreakdown']['linkedin'] + $analytics['contactBreakdown']['github']) }}</p>
            <p class="mt-1 text-xs text-ink/40">{{ $analytics['contactBreakdown']['linkedin'] }} LinkedIn &middot; {{ $analytics['contactBreakdown']['github'] }} GitHub</p>
        </div>
    </div>

    {{-- 30-day page-view chart --}}
    <div class="card mb-6">
        <div class="card-header">
            <h2 class="card-title">Page views &mdash; last 30 days</h2>
        </div>
        <div class="p-6">
            <canvas id="analytics-chart" height="96" data-series='@json($analytics["chart"])'></canvas>
        </div>
    </div>

    {{-- Top projects + recent events --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">Top projects</h2>
            </div>
            <div class="p-6">
                @forelse ($analytics['topProjects'] as $row)
                    <div class="flex items-center justify-between py-2 {{ ! $loop->last ? 'border-b border-ink/5' : '' }}">
                        <span class="text-sm font-semibold text-ink truncate pr-3">{{ $row->meta }}</span>
                        <span class="badge-accent shrink-0">{{ number_format($row->total) }}</span>
                    </div>
                @empty
                    <p class="text-sm text-ink/40">No project clicks yet.</p>
                @endforelse
            </div>
        </div>

        <div class="card lg:col-span-2 overflow-hidden">
            <div class="card-header">
                <h2 class="card-title">Recent activity</h2>
            </div>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Event</th>
                        <th>Detail</th>
                        <th>When</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($analytics['recentEvents'] as $event)
                        <tr>
                            <td class="cell-strong">{{ $eventLabels[$event->event] ?? $event->event }}</td>
                            <td>{{ $event->meta ?? '—' }}</td>
                            <td>{{ $event->created_at?->diffForHumans() ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-ink/40">No events recorded yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
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
