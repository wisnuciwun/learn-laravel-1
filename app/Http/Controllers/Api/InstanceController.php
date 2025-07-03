<?php
namespace App\Http\Controllers\Api;

use App\Helpers\ItsHelper;
use App\Models\Fianut\Apps;
use App\Models\Fianut\InstancePriviledges;
use App\Models\Fianut\Instances;
use App\Models\Fianut\InstanceSettings;
use App\Models\Fianut\Roles;
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
                    select('id', 'name', 'gender', 'is_owner', 'email', 'address', 'instance_code', 'active', 'nickname', 'created_at', 'sallary')->where('instance_code', $instanceCode)->where('is_owner', '!=', 1)->get();

               return response()->json([
                    'success' => true,
                    'message' => 'Get employees successfully',
                    'data' => $data,
               ], 200);
          } catch (\Throwable $th) {
               return response()->json([
                    'success' => false,
                    'message' => $th->getMessage(),
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
                    $dataUser = User::where('id', $userId)->first();
                    $dataInstance = Instances::where('instance_code', $instanceCode)->get();
               } else {
                    $dataUser = User::where('id', $userId)->select('name', 'nickname', 'gender', 'referral_code', 'email', 'address', 'active', 'is_owner', 'instance_code', 'sallary', 'poins', 'image')->first();
                    $dataInstance = Instances::where('id', $instanceId)->select('name', 'instance_code', 'address')->get();
               }

               $dataRole = UserPriviledges::with(['role'])->where('user_id', $userId)->select('id', 'role_id')->first();
               $dataInstanceSettings = InstanceSettings::where('instance_code', $instanceCode)->first();

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
                    'message' => $th->getMessage(),
               ], 500);
          }
     }

     public function appList(Request $request)
     {
          $userData = ItsHelper::verifyToken($request->token);

          $request->merge([
               // 'instance_id' => $userData->instance->id,
               'user_id' => $userData->id,
               'instance_code' => $userData->instance_code,
          ]);

          try {
               $data = Apps::with([
                    'appPayment' => function ($query) use ($request) {
                         $query->where('user_id', $request->user_id);
                    },
                    'instancePriviledge' => function ($query) use ($request) {
                         $query->where('instance_code', $request->instance_code);
                    },
                    'userPriviledge' => function ($query) use ($request) {
                         $query->where('user_id', $request->user_id);
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
                    'message' => $th->getMessage(),
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
                    'message' => $th->getMessage(),
               ], 500);
          }
     }

     public function manage(Request $request)
     {
          $userData = ItsHelper::verifyToken($request->token);
          $request->merge([
               'user_id' => $userData->id,
               'instance_code' => $userData->instance_code,
               'is_owner' => $userData->is_owner,
          ]);

          $success = true;
          $errors = '';
          $data = [];

          $validatedData = $request->validate([
               'name' => 'required|string|max:255'
          ]);

          try {
               $dataToSave = array_filter([
                    'name' => $validatedData['name'],
                    'address' => $request->address,
               ], fn($value) => !empty($value));

               if ($request->id) {
                    $data = Instances::where('id', $request->id)->first();

                    if ($data) {
                         $dataInstanceSetting = InstanceSettings::where('instance_code', $request->instance_code)->first();
                         $dataInstanceSettingToSave = [
                              'instance_type' => $request->instance_type,
                              'phone' => $request->phone
                         ];

                         if ($request->hasFile('img_instance_logo') && $request->file('img_instance_logo')->isValid()) {
                              if (!empty($dataInstanceSetting->img_instance_logo)) {
                                   $image = ItsHelper::saveImage('client', true, $data->img_instance_logo, $request, 'img_instance_logo');
                              } else {
                                   $image = ItsHelper::saveImage('client', false, null, $request, 'img_instance_logo');
                              }

                              $dataInstanceSetting['img_instance_logo'] = $image;
                         }

                         $dataInstanceSetting->update($dataInstanceSettingToSave);
                         $data->update($dataToSave);
                    } else {
                         $success = false;
                         $errors = 'Instance data not found';
                    }
               } else {
                    $dataInstancePriviledge = InstancePriviledges::where('instance_code', $request->instance_code)->first();

                    if ($dataInstancePriviledge && $request->is_owner == 1) {
                         $idRoleAppAdmin = Roles::where('name', 'Admin')->first();
                         $dataToSave['instance_code'] = $request->instance_code;
                         $dataToSave['user_id'] = $request->user_id;

                         // Create instance and get the saved model
                         $dataInstance = Instances::create($dataToSave);

                         // Fetch all priviledges with app_admin role for this instance_code
                         $dataUserPriviledge = UserPriviledges::where('user_id', $request->user_id)
                              ->where('role_id', $idRoleAppAdmin->id)
                              ->get();

                         $dataNewPriviledges = [];

                         foreach ($dataUserPriviledge as $priv) {
                              $dataNewPriviledges[] = [
                                   'user_id' => $priv->user_id,
                                   'role_id' => $priv->role_id,
                                   'instance_id' => $dataInstance->id,
                                   'app_id' => $priv->app_id,
                                   'created_at' => now(),
                                   'updated_at' => now(),
                              ];
                         }

                         if (!empty($dataNewPriviledges)) {
                              UserPriviledges::insert($dataNewPriviledges);
                         }
                    } else {
                         $success = false;
                         $errors = 'Not allowed to create new instance. Please subscribe at least 1 module by owner account.';
                    }
               }

               return response()->json([
                    'success' => $success,
                    'message' => $errors ?: "Successfully saved settings instance changes",
                    'data' => $data,
               ], $success ? 200 : 400);
          } catch (\Throwable $th) {
               return response()->json([
                    'success' => false,
                    'message' => $th->getMessage(),
               ], 500);
          }
     }

     public function instancePriviledge(Request $request)
     {
          try {
               $userData = ItsHelper::verifyToken($request->token);
               $request->merge([
                    // 'instance_id' => $userData->instance->id,
                    'instance_code' => $userData->instance_code,
                    'user_id' => $userData->id,
               ]);

               $data = InstancePriviledges::with(['instances'])->where('id', $request->instance_code)->first();

               return response()->json([
                    'success' => true,
                    'message' => 'Get instance priviledge data successfully',
                    'data' => $data,
               ], 200);
          } catch (\Throwable $th) {
               return response()->json([
                    'success' => false,
                    'message' => $th->getMessage(),
               ], 500);
          }
     }

     public function delete(Request $request)
     {
          $userData = ItsHelper::verifyToken($request->token);
          $request->merge([
               'instance_code' => $userData->instance->instance_code,
               'instance_id' => $userData->instance->id,
               'user_id' => $userData->id
          ]);

          $success = true;
          $errors = '';
          $data = [];

          $validatedData = $request->validate([
               'id' => 'required|array',
               'id.*' => 'integer'
          ]);

          try {
               if ($userData->is_owner == 1) {
                    $instance = Instances::whereIn('id', $validatedData['id'])
                         ->get();
                    $userPriviledges = UserPriviledges::whereIn('id', $validatedData['id'])
                         ->get();

                    if ($instance->isEmpty() && $userPriviledges->isEmpty()) {
                         $success = false;
                         $errors = 'No instance found to delete.';
                    } else {
                         $data = $instance->toArray();

                         Instances::whereIn('id', $instance->pluck('id'))->delete();
                         UserPriviledges::whereIn('id', $userPriviledges->pluck('id'))->delete();
                    }

               } else {
                    $success = false;
                    $errors = 'User not allowed';
               }

               return response()->json([
                    'success' => $success,
                    'message' => $errors ?: "Successfully delete instance",
                    'data' => $data,
               ], $success ? 200 : 400);
          } catch (\Exception $th) {
               return response()->json([
                    'success' => false,
                    'message' => $th->getMessage(),
               ], 500);
          }
     }
}