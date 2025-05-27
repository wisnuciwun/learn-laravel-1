<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    // use HasFactory;
    protected $table = 'x_posts';
    // pk
    public $primaryKey = 'id';
    public $timestamps = false;

    // post has a relationship with user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function image()
    {
        return $this->hasMany('App/Image', 'post_id');
    }

}