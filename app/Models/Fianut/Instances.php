<?php

namespace App\Models\Fianut;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Instances extends Model
{
     // use HasFactory;
     protected $table = 'instances';
     // pk
     public $primaryKey = 'id';

     public $timestamps = true;

     protected $guarded = ['id'];

     public function instanceSetting()
     {
          // return $this->belongsTo(AppPricings::class, 'app_pricings_id');
          return $this->belongsTo(InstanceSettings::class, 'instance_code', 'instance_code');
     }

     public function employee()
     {
          return $this->hasMany(User::class, 'instance_code', 'instance_code');
     }

}