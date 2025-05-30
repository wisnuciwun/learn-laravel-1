<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KKIdentity extends Model
{
     // use HasFactory;
     protected $table = 'psr_kk_identity';
     // pk
     public $primaryKey = 'blok';
     protected $casts = [
          'blok' => 'string',
     ];

     public $timestamps = false;

     protected $fillable = [
          'nama',
          'blok',
          'kk_path',
     ];
}