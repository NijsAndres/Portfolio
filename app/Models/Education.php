<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Education extends Model
{
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
