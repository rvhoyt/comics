<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasFactory;
    
    protected $fillable = [
      'strip_id',
      'user_id'
    ];
    
    public function owner() {
      return $this->belongsTo('App\Models\Strip');
    }
}
