<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    // use HasFactory;
    // protected $fillable = [
    //     'url',
    //     'post_id'
    // ];
    protected $table = 'images';
    public $primaryKey = 'id';

    public function post()
    {
        return $this->belongsTo('App\Product', 'id');
    }
}