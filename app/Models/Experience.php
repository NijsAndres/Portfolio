<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Experience extends Model
{
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
