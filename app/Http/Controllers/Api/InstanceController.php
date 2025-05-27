<?php
namespace App\Http\Controllers\Api;

use App\Models\Fianut\Apps;
use App\Models\Fianut\Instances;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use App\Models\Fianut\User;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;

class InstanceController extends Controller
{
     public function appList(Request $r)
     {
          try {
               $res = Apps::when($r->keyword, function ($q) use ($r) {
                    $q->where('name', 'like', "%{$r->keyword}%");
               })->get();

               return response()->json([
                    'success' => true,
                    'message' => 'Get app list successful',
                    'data' => $res,
               ], 200);
          } catch (\Throwable $th) {
               return response()->json([
                    'success' => false,
                    'errors' => $th->getMessage(),
               ], 500);
          }
     }

}