<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PsrNews extends Model
{
     // use HasFactory;
     protected $table = 'psr_news';
     // pk
     public $primaryKey = 'id';
     public $timestamps = false;
}