<?php

namespace App\Models\Fianut;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Roles extends Model
{
     // use HasFactory;
     protected $table = 'roles';
     // pk
     public $primaryKey = 'id';

     public $timestamps = true;

     protected $guarded = ['id'];

}