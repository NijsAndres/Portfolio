@extends('admin.layouts.app')

@section('title', 'Dashboard')

@section('content')
    @php $cvPath = \App\Models\SiteSetting::get('cv_path'); @endphp

    {{-- Stat cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 mb-8">
        @foreach (['projects' => 'Projects', 'education' => 'Education', 'experience' => 'Experience'] as $key => $label)
            <div class="bg-white rounded-lg border border-gray-200 p-6">
                <p class="text-sm font-medium text-gray-500">{{ $label }}</p>
                <p class="mt-2 text-3xl font-semibold text-gray-900">{{ $stats[$key] ?? 0 }}</p>
            </div>
        @endforeach
    </div>

    {{-- Singletons overview --}}
    <div class="bg-white rounded-lg border border-gray-200 mb-8">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="font-semibold text-gray-900">Site content</h2>
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-500">
                    <th class="px-6 py-3 font-medium">Section</th>
                    <th class="px-6 py-3 font-medium">Status</th>
                    <th class="px-6 py-3 font-medium">Last updated</th>
                    <th class="px-6 py-3 font-medium"></th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @php
                    $singletons = [
                        ['label' => 'Hero',    'model' => $hero,    'route' => 'admin.hero.edit'],
                        ['label' => 'About',   'model' => $about,   'route' => 'admin.about.edit'],
                        ['label' => 'Contact', 'model' => $contact, 'route' => 'admin.contact.edit'],
                    ];
                @endphp
                @foreach ($singletons as $row)
                    <tr>
                        <td class="px-6 py-3 font-medium text-gray-900">{{ $row['label'] }}</td>
                        <td class="px-6 py-3">
                            @if ($row['model'])
                                <span class="text-green-700">Configured</span>
                            @else
                                <span class="text-gray-400">Not set</span>
                            @endif
                        </td>
                        <td class="px-6 py-3 text-gray-500">
                            {{ $row['model']?->updated_at?->diffForHumans() ?? '—' }}
                        </td>
                        <td class="px-6 py-3 text-right">
                            <a href="{{ route($row['route']) }}" class="text-indigo-600 hover:text-indigo-800 font-medium">Edit</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- CV upload (no dedicated GET route; placed here per Step 6 decision) --}}
    <div id="cv-upload" class="bg-white rounded-lg border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="font-semibold text-gray-900">CV / Résumé</h2>
        </div>
        <div class="p-6">
            <p class="text-sm text-gray-600 mb-4">
                Current file:
                @if ($cvPath)
                    <span class="font-medium text-gray-900">{{ $cvPath }}</span>
                @else
                    <span class="text-gray-400">none uploaded yet</span>
                @endif
            </p>

            <form method="POST" action="{{ route('admin.cv.update') }}" enctype="multipart/form-data" class="flex items-center gap-4">
                @csrf
                <input type="file" name="cv" accept=".pdf"
                       class="block text-sm text-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" />
                <button type="submit"
                        class="bg-indigo-600 text-white text-sm font-medium px-4 py-2 rounded-md hover:bg-indigo-700 transition">
                    Upload CV
                </button>
            </form>
            <p class="mt-3 text-xs text-gray-400">PDF only, max 10&nbsp;MB. File storage is wired up in Step 8.</p>
        </div>
    </div>
@endsection
