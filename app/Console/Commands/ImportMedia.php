<?php

namespace App\Console\Commands;

use App\Models\HeroContent;
use App\Models\Media;
use App\Models\Project;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Register every existing image into the media library and link projects/hero
 * to their matching media record. Idempotent: re-running only adds files that
 * aren't recorded yet and backfills media_id where it is still null.
 *
 * Two sources are scanned, using the same relative-path convention as the
 * legacy image_path columns so paths line up for backfilling:
 *   - public/assets/**          → resolved via asset('assets/'.$path)
 *   - storage/app/public/**     → resolved via the public disk
 */
class ImportMedia extends Command
{
    protected $signature = 'media:import';

    protected $description = 'Import existing images into the media library and link projects/hero.';

    /** Raster types worth managing — decorative SVG icons are intentionally skipped. */
    private const EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

    public function handle(): int
    {
        $imported = 0;
        $imported += $this->importFrom(public_path('assets'), fn (string $rel) => $rel);
        $imported += $this->importFrom(storage_path('app/public'), fn (string $rel) => $rel);

        $this->info("Imported {$imported} new media file(s).");

        $linked = $this->backfill();
        $this->info("Linked {$linked} project/hero reference(s).");

        return self::SUCCESS;
    }

    /**
     * Scan a base directory for raster images and create a Media row per file.
     * $toPath maps the path-relative-to-base to the stored `path` value.
     */
    private function importFrom(string $base, callable $toPath): int
    {
        if (! File::isDirectory($base)) {
            return 0;
        }

        $count = 0;

        foreach (File::allFiles($base) as $file) {
            /** @var SplFileInfo $file */
            if (! in_array(strtolower($file->getExtension()), self::EXTENSIONS, true)) {
                continue;
            }

            $path = $toPath(str_replace('\\', '/', $file->getRelativePathname()));

            if (Media::where('path', $path)->exists()) {
                continue;
            }

            $dimensions = @getimagesize($file->getRealPath());

            Media::create([
                'path' => $path,
                'original_name' => $file->getFilename(),
                'mime_type' => File::mimeType($file->getRealPath()),
                'size' => $file->getSize(),
                'width' => $dimensions[0] ?? null,
                'height' => $dimensions[1] ?? null,
            ]);

            $count++;
        }

        return $count;
    }

    /**
     * Point each project/hero at the media row matching its current image_path,
     * but only where media_id is not already set.
     */
    private function backfill(): int
    {
        $byPath = Media::pluck('id', 'path');
        $linked = 0;

        foreach (Project::whereNull('media_id')->whereNotNull('image_path')->get() as $project) {
            if ($id = $byPath->get($project->image_path)) {
                $project->update(['media_id' => $id]);
                $linked++;
            }
        }

        foreach (HeroContent::whereNull('media_id')->whereNotNull('image_path')->get() as $hero) {
            if ($id = $byPath->get($hero->image_path)) {
                $hero->update(['media_id' => $id]);
                $linked++;
            }
        }

        return $linked;
    }
}
