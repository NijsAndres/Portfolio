<?php

namespace App\Models;

use App\Models\Concerns\HasSortOrder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Experience extends Model
{
    use HasSortOrder, HasTranslations;

    /** Per-locale JSON fields (spatie/laravel-translatable). */
    public array $translatable = ['role', 'period'];

    // Laravel would pluralise "Experience" to "experiences"; pin the real table.
    protected $table = 'experience';

    // No timestamp columns on this table.
    public $timestamps = false;

    protected $fillable = [
        'company',
        'role',
        'period',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];
}
