<?php

namespace App\Models;

use App\Models\Concerns\HasMediaImage;
use App\Models\Concerns\HasSortOrder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Project extends Model
{
    use HasMediaImage, HasSortOrder;

    protected $table = 'projects';

    // projects has full created_at/updated_at columns, so default timestamps apply.

    protected $fillable = [
        'title',
        'description',
        'tags',
        'url',
        'image_path',
        'media_id',
        'type',
        'body',
        'sort_order',
    ];

    protected $casts = [
        'tags' => 'array',
        'sort_order' => 'integer',
    ];

    /**
     * Filters this project is linked to (many-to-many via filter_project).
     * Separate from the free-text `tags`, which remain the visual card badge.
     */
    public function filters(): BelongsToMany
    {
        return $this->belongsToMany(Filter::class);
    }

    /** Default alt for the project image: the project title. */
    protected function imageAltFallback(): string
    {
        return $this->title;
    }
}
