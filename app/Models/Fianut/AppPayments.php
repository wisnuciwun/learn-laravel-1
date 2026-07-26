<?php

namespace App\Models\Fianut;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppPayments extends Model
{
     // use HasFactory;
     protected $table = 'app_payments';
     // pk
     public $primaryKey = 'id';

     public $timestamps = true;

     protected $guarded = ['id'];

     public function appPricing()
     {
          return $this->belongsTo(AppPricings::class, 'app_pricings_id');
     }

}