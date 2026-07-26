<?php

namespace App\Models\Fianut;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionsIn extends Model
{
     // use HasFactory;
     protected $table = 'transactions_in';
     // pk
     public $primaryKey = 'id';

     public $timestamps = true;

     protected $guarded = ['id'];

     public function inventory()
     {
          return $this->belongsTo(Inventory::class, 'inventory_id', 'id');
     }

}