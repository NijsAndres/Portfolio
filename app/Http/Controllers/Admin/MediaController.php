<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

/**
 * Media library. Uploads land on the public disk under media/, and every file
 * is recorded with its metadata (size, dimensions, mime). The library page and
 * the project/hero pickers all read from here.
 *
 * No create/edit pages: uploading and metadata editing happen in modals on the
 * index, so only index/store/update/destroy are wired up in routes/web.php.
 */
class MediaController extends Controller
{
    public function index()
    {
        $media = Media::latest()->get();

        return view('admin.media.index', compact('media'));
    }

    /**
     * Store one or more uploads. The library page posts files[] (multi-upload);
     * the in-modal picker posts a single file and expects JSON back so it can
     * append + auto-select the new item without a page reload.
     */
    public function store(Request $request)
    {
        $request->validate([
            'files' => ['nullable', 'array'],
            'files.*' => ['image', 'mimes:jpg,jpeg,png,webp,gif', 'max:8192'],
            'file' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:8192'],
        ]);

        $uploads = $request->file('files', []);
        if ($request->hasFile('file')) {
            $uploads[] = $request->file('file');
        }

        $created = collect($uploads)->map(fn (UploadedFile $file) => $this->storeFile($file));

        if ($request->expectsJson()) {
            return response()->json([
                'media' => $created->map(fn (Media $m) => [
                    'id' => $m->id,
                    'url' => $m->url,
                    'alt' => $m->alt,
                    'title' => $m->title ?: $m->original_name,
                ])->values(),
            ]);
        }

        $count = $created->count();

        return back()->with('success', $count === 1 ? 'Image uploaded.' : "{$count} images uploaded.");
    }

    public function update(Request $request, Media $media)
    {
        $media->update($this->validateData($request));

        if ($request->expectsJson()) {
            return response()->json(['status' => 'ok']);
        }

        return back()->with('success', 'Media updated.');
    }

    public function destroy(Media $media)
    {
        // Delete the file only when it lives on the public disk; imported legacy
        // public/assets paths are left in place. The media_id FK is nullOnDelete,
        // so any project/hero reference is cleared automatically.
        if ($media->path && Storage::disk('public')->exists($media->path)) {
            Storage::disk('public')->delete($media->path);
        }

        $media->delete();

        return back()->with('success', 'Media deleted.');
    }

    /**
     * Persist a single uploaded file and record its metadata.
     */
    private function storeFile(UploadedFile $file): Media
    {
        $dimensions = @getimagesize($file->getRealPath());
        $path = $file->store('media', 'public');

        return Media::create([
            'path' => $path,
            'original_name' => $file->getClientOriginalName(),
            'mime_type' => $file->getClientMimeType(),
            'size' => $file->getSize(),
            'width' => $dimensions[0] ?? null,
            'height' => $dimensions[1] ?? null,
        ]);
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'alt' => ['nullable', 'string', 'max:255'],
            'title' => ['nullable', 'string', 'max:255'],
            'caption' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);
    }
}
