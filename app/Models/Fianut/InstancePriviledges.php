<?php

namespace App\Models\Fianut;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InstancePriviledges extends Model
{
     // use HasFactory;
     protected $table = 'instance_priviledges';
     // pk
     public $primaryKey = 'id';

     public $timestamps = true;

     protected $guarded = ['id'];

     public function app()
     {
          return $this->belongsTo(Apps::class, 'app_id', 'id');
     }

     public function payments()
     {
          return $this->hasMany(AppPayments::class, 'instance_code', 'instance_code');
     }

     public function appPricing()
     {
          return $this->belongsTo(AppPricings::class, 'app_pricings_id');
     }
}