<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Audit extends Model
{
  protected $fillable = [
    'user_id',
    'affected_module',
    'action_performed',
    'date_time',
    'detail',
  ];

  public function user()
  {
    return $this->belongsTo(User::class, 'user_id', 'id');
  }
}
