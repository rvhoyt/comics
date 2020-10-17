<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Strip extends Model
{
    protected $fillable = [
      'title',
      'description',
      'url',
      'user',
      'fileId'
    ];
    
    public function owner() {
      return $this->belongsTo('App\Models\User', 'user');
    }
    
    public function likes()
    {
        return $this->hasMany('App\Models\Like');
    }
}
