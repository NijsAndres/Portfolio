<?php

namespace App\Models;

use App\Models\Concerns\HasSortOrder;
use Illuminate\Database\Eloquent\Model;

class Experience extends Model
{
    use HasSortOrder;

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
