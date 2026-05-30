<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $table = 'projects';

    // projects has full created_at/updated_at columns, so default timestamps apply.

    protected $fillable = [
        'title',
        'description',
        'tags',
        'url',
        'image_path',
        'type',
        'body',
        'sort_order',
    ];

    protected $casts = [
        'tags' => 'array',
        'sort_order' => 'integer',
    ];
}
