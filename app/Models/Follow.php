<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Follow extends Model
{
    use HasFactory;
    
    protected $fillable = [
      'user_id',
      'followee_id'
    ];
    
    public function owner() {
      return $this->hasOne('App\Models\User');
    }
    
    public function followee() {
      return $this->hasOne('App\Models\User', 'id', 'followee_id');
    }
}
