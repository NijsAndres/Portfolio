<?php

namespace App\Models;

use App\Models\Concerns\ResolvesStorageUrl;
use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    use ResolvesStorageUrl;

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

    /** Resolve the stored path to a usable URL (public disk, else public/assets/). */
    public function getUrlAttribute(): ?string
    {
        return $this->resolveStorageUrl($this->path);
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
