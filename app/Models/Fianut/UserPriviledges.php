<?php

namespace App\Models\Fianut;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPriviledges extends Model
{
     // use HasFactory;
     protected $table = 'user_priviledges';
     // pk
     public $primaryKey = 'id';

     public $timestamps = true;

     protected $guarded = ['id'];

     public function app()
     {
          return $this->belongsTo(Apps::class, 'app_id', 'id');
     }

     public function role()
     {
          return $this->belongsTo(Roles::class, 'role_id', 'id');
     }

}