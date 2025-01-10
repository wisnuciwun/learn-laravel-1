<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PsrFoods extends Model
{
     // use HasFactory;
     protected $table = 'psr_foods';
     // pk
     public $primaryKey = 'id';
     public $timestamps = false;
}