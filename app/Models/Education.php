<?php

namespace App\Models;

use App\Models\Concerns\HasSortOrder;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Education extends Model
{
    use HasSortOrder, HasTranslations;

    /** Per-locale JSON fields (spatie/laravel-translatable). */
    public array $translatable = ['degree', 'period'];

    // Laravel would pluralise "Education" to "educations"; pin the real table.
    protected $table = 'education';

    // No timestamp columns on this table.
    public $timestamps = false;

    protected $fillable = [
        'institution',
        'degree',
        'period',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];
}
