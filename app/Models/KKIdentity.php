<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KKIdentity extends Model
{
     // use HasFactory;
     protected $table = 'kk_identity';
     // pk
     public $primaryKey = 'blok';
     public $timestamps = false;

     protected $fillable = [
          'nama',
          'blok',
          'kk_path',
     ];
}