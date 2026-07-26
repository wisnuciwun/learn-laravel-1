<?php

namespace App\Models\Fianut;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Apps extends Model
{
     // use HasFactory;
     protected $table = 'apps';
     // pk
     public $primaryKey = 'id';

     public $timestamps = true;

     protected $guarded = ['id'];

     public function instancePriviledge()
     {
          return $this->hasOne(InstancePriviledges::class, 'app_id', 'id');
     }

     public function userPriviledge()
     {
          return $this->hasOne(UserPriviledges::class, 'app_id', 'id');
     }

     public function appPricing()
     {
          return $this->hasMany(AppPricings::class, 'app_id', 'id');
     }

     public function appPayment()
     {
          return $this->hasOne(AppPayments::class, 'app_id', 'id')
               ->whereNull('confirm_payment')
               ->latest('created_at');
     }
}