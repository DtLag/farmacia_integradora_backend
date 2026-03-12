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
}
