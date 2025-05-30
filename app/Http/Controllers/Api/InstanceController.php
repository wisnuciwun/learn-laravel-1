<?php
namespace App\Http\Controllers\Api;

use App\Helpers\ItsHelper;
use App\Models\Fianut\Apps;
use App\Models\Fianut\InstancePriviledges;
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
     public function appList(Request $request)
     {
          $userData = ItsHelper::verifyToken($request->token);
          $instanceId = $userData->instance_id;
          $userId = $userData->id;

          try {
               // $res = Apps::with([
               //      'instancePriviledge' => fn($q) => $q->where('instance_id', $instanceId),
               //      'userPriviledge' => fn($q) => $q->where('user_id', $userId),
               // ])->when($request->keyword, fn($q) => $q->where('name', 'like', "%{$request->keyword}%"))->get();

               $res = Apps::with([
                    'instancePriviledge' => function ($query) use ($instanceId) {
                         $query->where('instance_id', $instanceId);
                    },
                    'userPriviledge' => function ($query) use ($userId) {
                         $query->where('user_id', $userId);
                    }
               ])->when($request->keyword, fn($q) => $q->where('name', 'like', "%{$request->keyword}%"))
                    ->get();


               return response()->json([
                    'success' => true,
                    'message' => 'Get priviledge apps list successfully',
                    'data' => $res,
               ], 200);
          } catch (\Throwable $th) {
               return response()->json([
                    'success' => false,
                    'errors' => $th->getMessage(),
               ], 500);
          }
     }

     public function detail(Request $request)
     {
          try {
               $userData = ItsHelper::verifyToken($request->token);
               $request->merge([
                    'instance_id' => $userData->instance->id,
                    'user_id' => $userData->id,
               ]);

               $res = Instances::where('id', $request->instance_id)->first();

               return response()->json([
                    'success' => true,
                    'message' => 'Get instance data successfully',
                    'data' => $res,
               ], 200);
          } catch (\Throwable $th) {
               return response()->json([
                    'success' => false,
                    'errors' => $th->getMessage(),
               ], 500);
          }
     }

     public function users(Request $request)
     {
          try {
               $userData = ItsHelper::verifyToken($request->token);
               $request->merge([
                    'instance_id' => $userData->instance->id,
                    'user_id' => $userData->id,
               ]);

               $res = User::where('id', $request->instance_id)->first();

               return response()->json([
                    'success' => true,
                    'message' => 'Get instance data successfully',
                    'data' => $res,
               ], 200);
          } catch (\Throwable $th) {
               return response()->json([
                    'success' => false,
                    'errors' => $th->getMessage(),
               ], 500);
          }
     }

     public function instancePriviledge(Request $request)
     {
          try {
               $userData = ItsHelper::verifyToken($request->token);
               $request->merge([
                    'instance_id' => $userData->instance->id,
                    'user_id' => $userData->id,
               ]);

               $res = InstancePriviledges::with(['instances'])->where('id', $request->instance_id)->first();

               return response()->json([
                    'success' => true,
                    'message' => 'Get instance priviledge data successfully',
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