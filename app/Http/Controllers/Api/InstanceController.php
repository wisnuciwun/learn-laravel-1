<?php
namespace App\Http\Controllers\Api;

use App\Helpers\ItsHelper;
use App\Models\Fianut\Apps;
use App\Models\Fianut\InstancePriviledges;
use App\Models\Fianut\Instances;
use App\Models\Fianut\InstanceSettings;
use App\Models\Fianut\UserPriviledges;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use App\Models\Fianut\User;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;

class InstanceController extends Controller
{
     public function employees(Request $request)
     {
          $userData = ItsHelper::verifyToken($request->token);
          $userId = $userData->id;
          $instanceCode = $userData->instance_code;

          try {
               $data = User::with(['userPriviledges.app:id,name,link', 'userPriviledges.role:id,name,tabs'])->
                    select('id', 'name', 'gender', 'is_owner', 'email', 'address', 'instance_code', 'active', 'nickname')->where('instance_code', $instanceCode)->where('is_owner', '!=', 1)->get();

               return response()->json([
                    'success' => true,
                    'message' => 'Get employees successfully',
                    'data' => $data,
               ], 200);
          } catch (\Throwable $th) {
               return response()->json([
                    'success' => false,
                    'errors' => $th->getMessage(),
               ], 500);
          }
     }

     public function profile(Request $request)
     {
          $userData = ItsHelper::verifyToken($request->token);
          $userId = $userData->id;
          $instanceId = $userData->instance->id;
          $instanceCode = $userData->instance->instance_code;

          try {
               if ($userData->is_owner == 1) {
                    $dataUser = User::where('id', $userId)->select('name', 'gender', 'referral_code', 'email', 'address', 'income', 'outcome', 'target_per_month', 'saving', 'active', 'is_owner')->first();
               } else {
                    $dataUser = User::where('id', $userId)->select('name', 'gender', 'referral_code', 'email', 'address', 'active', 'is_owner')->first();

               }
               $dataUser = User::where('id', $userId)->select('name', 'gender', 'referral_code', 'email', 'address', 'income', 'outcome', 'target_per_month', 'saving', 'active', 'is_owner')->first();
               $dataInstance = Instances::where('id', $instanceId)->select('name', 'instance_code')->get();
               $dataInstanceSettings = InstanceSettings::where('instance_code', $instanceCode)->select('hello_template_id', 'instance_code', 'title', 'slogan', 'promotion', 'third_party_links', 'img_heading')->first();
               $dataRole = UserPriviledges::with(['role'])->where('user_id', $userId)->select('id', 'role_id')->first();

               return response()->json([
                    'success' => true,
                    'message' => 'Get profile successfully',
                    'data' => [
                         'user' => $dataUser,
                         'instance' => $dataInstance,
                         'instance_settings' => $dataInstanceSettings,
                         'role' => $dataRole,
                    ],
               ], 200);
          } catch (\Throwable $th) {
               return response()->json([
                    'success' => false,
                    'errors' => $th->getMessage(),
               ], 500);
          }
     }

     public function appList(Request $request)
     {
          $userData = ItsHelper::verifyToken($request->token);
          $instanceId = $userData->instance->id;
          $userId = $userData->id;

          try {
               $data = Apps::with([
                    'instancePriviledge' => function ($query) use ($instanceId) {
                         $query->where('instance_id', $instanceId);
                    },
                    'userPriviledge' => function ($query) use ($userId) {
                         $query->where('user_id', $userId);
                    }
               ])->when($request->keyword, fn($q) => $q->where('name', 'like', "%{$request->keyword}%"))->get();

               return response()->json([
                    'success' => true,
                    'message' => 'Get priviledge apps list successfully',
                    'data' => $data,
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

               $data = Instances::where('id', $request->instance_id)->first();

               return response()->json([
                    'success' => true,
                    'message' => 'Get instance data successfully',
                    'data' => $data,
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

               $data = InstancePriviledges::with(['instances'])->where('id', $request->instance_id)->first();

               return response()->json([
                    'success' => true,
                    'message' => 'Get instance priviledge data successfully',
                    'data' => $data,
               ], 200);
          } catch (\Throwable $th) {
               return response()->json([
                    'success' => false,
                    'errors' => $th->getMessage(),
               ], 500);
          }
     }
}