<?php

namespace App\Models\Concerns;

use Illuminate\Support\Facades\Storage;

/**
 * Two-tier resolution of a stored relative path to a usable URL: new uploads
 * live on the public disk and resolve to /storage/...; legacy seeded paths
 * (e.g. 'projects/banksy.webp') fall back to the files shipped in public/assets/.
 * Returns null when no path is set. Shared by Media, Project, HeroContent.
 */
trait ResolvesStorageUrl
{
    protected function resolveStorageUrl(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        return Storage::disk('public')->exists($path)
            ? Storage::disk('public')->url($path)
            : asset('assets/'.$path);
    }
}
