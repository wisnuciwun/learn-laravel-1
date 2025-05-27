<?php
namespace App\Http\Controllers\Api;

use App\Helpers\InstanceHelper;
use App\Models\Fianut\Apps;
use App\Models\Fianut\Instances;
use App\Models\Fianut\Texts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use App\Models\Fianut\User;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;

class HelloController extends Controller
{
     public function templateList(Request $r)
     {
          try {
               $res = Texts::where('app_id', 1)->when($r->keyword, function ($q) use ($r) {
                    $q->where('name', 'like', "%{$r->keyword}%");
               })->get();

               return response()->json([
                    'success' => true,
                    'message' => 'Get hello template list successful',
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