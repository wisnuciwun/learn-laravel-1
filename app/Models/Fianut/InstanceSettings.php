<?php

namespace App\Models\Fianut;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstanceSettings extends Model
{
     // use HasFactory;
     protected $table = 'instance_settings';
     // pk
     public $primaryKey = 'id';

     public $timestamps = true;

     protected $guarded = ['id'];

     // TODO expired instance or not priviledge yet must not seen on google
     // public function hasPriviledge()
     // {
     //      return $this->hasOne(InstancePriviledges::class, 'app_id', 'id')
     //           ->whereNotNull('expired_at');
     // }
}