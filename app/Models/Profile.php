<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;
    
    protected $fillable = [
      'description',
      'image',
      'fileId',
      'user_id'
    ];
    
    public function owner() {
      return $this->belongsTo('App\Models\User');
    }
}
