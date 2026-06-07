<?php

namespace App\Models;

use App\Models\Concerns\HasSortOrder;
use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
    use HasSortOrder;

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
