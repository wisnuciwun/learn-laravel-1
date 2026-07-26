<?php

namespace App\Models\Fianut;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionsOut extends Model
{
     // use HasFactory;
     protected $table = 'transactions_out';
     // pk
     public $primaryKey = 'id';

     public $timestamps = true;

     protected $guarded = ['id'];

}