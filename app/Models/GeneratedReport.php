<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class GeneratedReport extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'report_type',
        'starting_range',
        'end_range',
        'user_id',
        'file_location',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
