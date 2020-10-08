<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Library extends Model
{
    use HasFactory;
    
    protected $fillable = [
      'data',
      'user_id'
    ];
    
    public function owner() {
      return $this->belongsTo('App\Models\User');
    }
}
