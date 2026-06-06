<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    protected $table = 'media';

    protected $fillable = [
        'path',
        'original_name',
        'mime_type',
        'size',
        'width',
        'height',
        'alt',
        'title',
        'caption',
        'description',
    ];

    protected $casts = [
        'size' => 'integer',
        'width' => 'integer',
        'height' => 'integer',
    ];

    /**
     * Resolve the stored path to a usable URL. Mirrors the two-tier logic used
     * for project/hero images: new uploads live on the public disk and resolve
     * to /storage/...; imported legacy paths (e.g. 'projects/banksy.webp') fall
     * back to the files shipped in public/assets/.
     */
    public function getUrlAttribute(): ?string
    {
        if (! $this->path) {
            return null;
        }

        if (Storage::disk('public')->exists($this->path)) {
            return Storage::disk('public')->url($this->path);
        }

        return asset('assets/'.$this->path);
    }

    /**
     * Human-friendly file size for the metadata panel.
     */
    public function getHumanSizeAttribute(): string
    {
        $bytes = $this->size ?? 0;

        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 1).' MB';
        }

        if ($bytes >= 1024) {
            return round($bytes / 1024).' KB';
        }

        return $bytes.' B';
    }
}
