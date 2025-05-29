<?php
namespace App\Http\Controllers\Api;

use App\Helpers\ItsHelper;
use App\Models\Fianut\AppPayments;
use App\Models\Fianut\AppPricings;
use App\Models\Fianut\Apps;
use App\Models\Fianut\InstancePriviledges;
use App\Models\Fianut\Instances;
use App\Models\Fianut\InstanceTypes;
use App\Models\Fianut\Roles;
use App\Models\Fianut\Texts;
use App\Models\Fianut\UserPriviledges;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use App\Models\Fianut\User;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;

class AdminController extends Controller
{
     public function appList(Request $request)
     {
          try {
               $res = Apps::when($request->keyword, function ($q) use ($request) {
                    $q->where('name', 'like', "%{$request->keyword}%");
               })
                    ->when($request->limit, function ($q) use ($request) {
                         $q->limit($request->limit);
                    })
                    ->when($request->sort_by, function ($q) use ($request) {
                         $q->orderBy($request->sort_by);
                    })
                    ->get();

               return response()->json([
                    'success' => true,
                    'message' => 'Get app list successfully',
                    'data' => $res,
               ], 200);
          } catch (\Throwable $th) {
               return response()->json([
                    'success' => false,
                    'errors' => $th->getMessage(),
               ], 500);
          }
     }

     public function manageApp(Request $request)
     {
          ItsHelper::verifyAsAdmin($request->token);

          $success = true;
          $errors = '';
          $validatedData = $request->validate([
               'name' => 'required|string|max:255',
               'link' => 'required|string|max:500',
               'description' => 'required',
               'short_description' => 'required|string|max:150',
               'image' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
          ]);

          try {
               if ($request->id) {
                    $data = Apps::where('id', $request->id)->first();

                    if ($data) {
                         $dataToUpdate = [
                              'name' => $validatedData['name'],
                              'link' => $validatedData['link'],
                              'description' => $validatedData['description'],
                              'short_description' => $validatedData['short_description'],
                         ];

                         if (!empty($validatedData['image'])) {
                              $image = ItsHelper::saveImage('system', true, $data->image, $request);
                              $dataToUpdate['image'] = $image;
                         }

                         $data->update($dataToUpdate);
                    } else {
                         $success = false;
                         $errors = 'App data not found';
                    }
               } else {
                    $dataToCreate = [
                         'name' => $validatedData['name'],
                         'link' => $validatedData['link'],
                         'description' => $validatedData['description'],
                         'short_description' => $validatedData['short_description'],
                    ];

                    if (!empty($validatedData['image'])) {
                         $image = ItsHelper::saveImage('system', false, null, $request);
                         $dataToCreate['image'] = $image;
                         $data = Apps::create($dataToCreate)->save();
                    } else {
                         $success = false;
                         $errors = 'No image uploaded';
                    }
               }

               return response()->json([
                    'success' => $success,
                    'message' => $errors ? '' : "Successfully saved app changes",
                    'data' => $data,
                    'errors' => $errors
               ], 200);
          } catch (\Exception $th) {
               return response()->json([
                    'success' => false,
                    'errors' => $th->getMessage(),
               ], 500);
          }
     }

     public function managePricing(Request $request)
     {
          ItsHelper::verifyAsAdmin($request->token);

          $success = true;
          $errors = '';
          $validatedData = $request->validate([
               'name' => 'required|string|max:100',
               'price' => 'required|integer',
               'member_type' => 'required|integer',
               'app_id' => 'required|integer',
          ]);

          try {
               $dataToSave = [
                    'name' => $validatedData['name'],
                    'price' => $validatedData['link'],
                    'member_type' => $validatedData['description'],
                    'app_id' => $validatedData['app_id'],
               ];

               if ($request->id) {
                    $dataApp = Apps::where('id', $request->app_id)->first();
                    $data = AppPricings::where('id', $request->id)->first();

                    if ($data && $dataApp) {
                         $data->update($dataToSave);
                    } else {
                         $success = false;
                         $errors = 'App data or pricing not found';
                    }
               } else {
                    $data = Apps::create($dataToSave)->save();
               }

               return response()->json([
                    'success' => $success,
                    'message' => $errors ? '' : "Successfully saved app pricing changes",
                    'data' => $data,
                    'errors' => $errors
               ], 200);
          } catch (\Throwable $th) {
               return response()->json([
                    'success' => false,
                    'errors' => $th->getMessage(),
               ], 500);
          }
     }

     public function manageInstancePriviledges(Request $request)
     {
          $userData = ItsHelper::verifyToken($request->token);
          $request->merge([
               'instance_id' => $userData->instance->id,
               'user_id' => $userData->id,
          ]);

          $success = true;
          $errors = '';
          $validatedData = $request->validate([
               'app_id' => 'required',
               'instance_id' => 'required',
               'user_id' => 'required',
               'app_pricings_id' => 'required',
          ]);

          try {
               $dataInstance = Instances::where('id', $request->instance_id)->first();
               $dataUser = User::where('id', $request->user_id)->first();
               $dataApp = Apps::where('id', $request->app_id)->first();
               $dataToSave = [
                    'app_id' => $validatedData['app_id'],
                    'instance_id' => $validatedData['instance_id'],
                    'user_id' => $validatedData['user_id'],
                    'app_pricings_id' => $validatedData['app_pricings_id'],
                    'expired_at' => Carbon::now()->addDays(30)
               ];

               if ($request->id) {
                    $data = InstancePriviledges::where('id', $request->id)->first();

                    if ($data && $dataApp && $dataInstance && $dataUser) {
                         $data->update($dataToSave);
                    } else {
                         $success = false;
                         $errors = 'Required data not found';
                    }
               } else {
                    if ($dataApp && $dataInstance && $dataUser) {
                         $data = InstancePriviledges::create($dataToSave)->save();
                    } else {
                         $success = false;
                         $errors = 'Required data not found';
                    }
               }

               return response()->json([
                    'success' => $success,
                    'message' => $errors ? '' : "Successfully saved app pricing changes",
                    'data' => $data,
                    'errors' => $errors
               ], 200);
          } catch (\Throwable $th) {
               return response()->json([
                    'success' => false,
                    'errors' => $th->getMessage(),
               ], 500);
          }
     }

     public function instanceTypes(Request $request)
     {
          try {
               $res = InstanceTypes::when($request->keyword, function ($q) use ($request) {
                    $q->where('name', 'like', "%{$request->keyword}%");
               })->get();

               return response()->json([
                    'success' => true,
                    'message' => 'Get instance type list successfully',
                    'data' => $res,
               ], 200);
          } catch (\Throwable $th) {
               return response()->json([
                    'success' => false,
                    'errors' => $th->getMessage(),
               ], 500);
          }
     }

     public function manageInstanceTypes(Request $request)
     {
          ItsHelper::verifyAsAdmin($request->token);

          $success = true;
          $errors = '';
          $validatedData = $request->validate([
               'name' => 'required|string|max:100'
          ]);

          try {
               $dataToSave = [
                    'name' => $validatedData['name'],
               ];

               if ($request->id) {
                    $data = InstanceTypes::where('id', $request->id)->first();

                    if ($data) {
                         $data->update($dataToSave);
                    } else {
                         $success = false;
                         $errors = 'Instance type not found';
                    }
               } else {
                    $data = InstanceTypes::create($dataToSave)->save();
               }

               return response()->json([
                    'success' => $success,
                    'message' => $errors ? '' : "Successfully saved instances type changes",
                    'data' => $data,
                    'errors' => $errors
               ], 200);
          } catch (\Throwable $th) {
               return response()->json([
                    'success' => false,
                    'errors' => $th->getMessage(),
               ], 500);
          }
     }

     public function manageTexts(Request $request)
     {
          ItsHelper::verifyToken($request->token);

          $success = true;
          $errors = '';
          $validatedData = $request->validate([
               'title' => 'required|string|max:100',
               'name' => 'required|string|max:100'
          ]);

          try {
               $dataToSave = [
                    'name' => $validatedData['name'],
                    'title' => $validatedData['title'],
                    'data' => $request->data,
                    'type' => $request->type,
                    'instance_id' => $request->instance_id,
                    'app_id' => $request->app_id,
               ];

               if ($request->id) {
                    $data = Texts::where('id', $request->id)->first();

                    if ($data) {
                         $data->update($dataToSave);
                    } else {
                         $success = false;
                         $errors = 'Texts data not found';
                    }
               } else {
                    $data = Texts::create($dataToSave)->save();
               }

               return response()->json([
                    'success' => $success,
                    'message' => $errors ? '' : "Successfully saved texts changes",
                    'data' => $data,
                    'errors' => $errors
               ], 200);
          } catch (\Throwable $th) {
               return response()->json([
                    'success' => false,
                    'errors' => $th->getMessage(),
               ], 500);
          }
     }

     public function manageUserPriviledges(Request $request)
     {
          $userData = ItsHelper::verifyToken($request->token);
          $request->merge([
               'instance_id' => $userData->instance->id,
               'user_id' => $userData->id,
          ]);

          $success = true;
          $errors = '';
          $validatedData = $request->validate([
               'user_id' => 'required',
               'instance_id' => 'required',
               'role_id' => 'required',
               'app_id' => 'required',
          ]);

          try {
               $dataInstance = Instances::where('id', $request->instance_id)->first();
               $dataUser = User::where('id', $request->user_id)->first();
               $dataApp = Apps::where('id', $request->app_id)->first();
               $dataToSave = [
                    'app_id' => $validatedData['app_id'],
                    'instance_id' => $validatedData['instance_id'],
                    'user_id' => $validatedData['user_id'],
                    'role_id' => $validatedData['role_id'],
               ];

               if ($request->id) {
                    $data = UserPriviledges::where('id', $request->id)->first();

                    if ($data && $dataApp && $dataInstance && $dataUser) {
                         $data->update($dataToSave);
                    } else {
                         $success = false;
                         $errors = 'Required data not found';
                    }
               } else {
                    if ($dataApp && $dataInstance && $dataUser) {
                         $data = UserPriviledges::create($dataToSave)->save();
                    } else {
                         $success = false;
                         $errors = 'Required data not found';
                    }
               }

               return response()->json([
                    'success' => $success,
                    'message' => $errors ? '' : "Successfully saved user priviledge changes",
                    'data' => $data,
                    'errors' => $errors
               ], 200);
          } catch (\Throwable $th) {
               return response()->json([
                    'success' => false,
                    'errors' => $th->getMessage(),
               ], 500);
          }
     }

     public function requestPayment(Request $request)
     {
          $userData = ItsHelper::verifyToken($request->token);
          $request->merge([
               'instance_id' => $userData->instance->id,
               'user_id' => $userData->id,
          ]);

          $success = true;
          $errors = '';
          $validatedData = $request->validate([
               'user_id' => 'required',
               'instance_id' => 'required',
               'app_id' => 'required',
               'app_pricings_id' => 'required',
          ]);

          try {
               // Step 1: Check user access/privilege
               $priv = InstancePriviledges::where('user_id', $request->user_id)
                    ->where('instance_id', $request->instance_id)
                    ->where('app_id', $request->app_id)
                    ->first();

               if (!$priv) {
                    $dataNewInstancePriviledge = [
                         'user_id' => $validatedData['user_id'],
                         'instance_id' => $validatedData['instance_id'],
                         'app_id' => $validatedData['app_id'],
                         'app_pricings_id' => $validatedData['app_pricings_id'],
                         'expired_at' => null,
                    ];

                    InstancePriviledges::create($dataNewInstancePriviledge);
               }

               $now = Carbon::now();
               $expiredAt = Carbon::parse($priv->expired_at);
               $daysDiff = $now->diffInDays($expiredAt, false); // negative if expired

               // Step 2: Enforce time window
               if ($daysDiff > 7) {
                    return response()->json([
                         'success' => false,
                         'errors' => 'Too early to re-subscribe. Try again within 7 days before expiry.',
                    ], 403);
               }

               if ($daysDiff < -7) {
                    return response()->json([
                         'success' => false,
                         'errors' => 'Subscription has expired too long ago. Please contact support.',
                    ], 403);
               }

               // Step 3: Prevent duplicate pending payments
               $exists = AppPayments::where('user_id', $request->user_id)
                    ->where('instance_id', $request->instance_id)
                    ->where('app_id', $request->app_id)
                    ->whereNull('confirm_payment')
                    ->exists();

               if ($exists) {
                    return response()->json([
                         'success' => false,
                         'errors' => 'There is already a pending payment for this app.',
                    ], 409);
               }

               // Step 4: Generate unique transaction_id
               $instanceCode = $dataInstance->instance_code ?? 'GEN'; // fallback if instance_code is missing
               $transactionId = ItsHelper::generateTransactionCode($instanceCode);

               // Step 5: Save payment request
               $dataToSave = [
                    'user_id' => $validatedData['user_id'],
                    'instance_id' => $validatedData['instance_id'],
                    'app_id' => $validatedData['app_id'],
                    'app_pricings_id' => $validatedData['app_pricings_id'],
                    'transaction_id' => $transactionId,
                    'confirm_payment' => null,
               ];

               $data = AppPayments::create($dataToSave);

               return response()->json([
                    'success' => $success,
                    'message' => $errors ? '' : "Successfully saved app pricing changes",
                    'data' => $data,
                    'errors' => $errors
               ], 200);
          } catch (\Throwable $th) {
               return response()->json([
                    'success' => false,
                    'errors' => $th->getMessage(),
               ], 500);
          }
     }

     public function confirmPayment(Request $request)
     {
          ItsHelper::verifyAsAdmin($request->token);

          $success = true;
          $errors = '';
          $validatedData = $request->validate([
               'transaction_id' => 'required',
               'confirm_payment' => 'required',
               'amount' => 'required|integer'
          ]);

          try {
               $dataTransaction = AppPayments::with(['appPricing:id,name,price'])
                    ->where('transaction_id', $request->transaction_id)
                    ->first();
               $dataUsers = User::where('instance_code')
                    ->where('is_owner', '!=', 1)
                    ->select('id', 'name'); // TODO: we have to check unactive user
               $dataToSave = [
                    'confirm_payment' => $validatedData['confirm_payment'],
               ];
               $userCount = (clone $dataUsers)->count();
               $price = optional($dataTransaction->appPricing)->price ?? 0;
               $shouldPay = $price * $userCount;

               if ($request->amount == $shouldPay) {
                    if ($dataTransaction) {
                         $dataInstancePriviledges = InstancePriviledges::
                              where('id', $dataTransaction->instance_id)
                              ->where('app_id', $dataTransaction->app_id)
                              ->first();

                         if ($dataInstancePriviledges) {
                              $dataToSave['expired_at'] = Carbon::parse($dataInstancePriviledges->expired_at ?? now())->addDays(30);
                              $dataInstancePriviledges->update($dataToSave);
                         } else {
                              $dataToSave['app_id'] = $dataTransaction->app_id;
                              $dataToSave['instance_id'] = $dataTransaction->instance_id;
                              $dataToSave['user_id'] = $dataTransaction->user_id;
                              $dataToSave['app_pricings_id'] = $dataTransaction->app_pricings_id;
                              $dataToSave['expired_at'] = Carbon::now()->addDays(30);
                              $data = InstancePriviledges::create($dataToSave)->save();

                              $dataOwner = User::where('instance_code')
                                   ->where('is_owner', '==', 1)
                                   ->where('id', $dataTransaction->user_id)
                                   ->count();
                              $idRoleAppAdmin = Roles::where('name', 'app_admin')->first();

                              if ($dataOwner) {
                                   $dataNewPriviledge = [
                                        'user_id' => $dataTransaction->user_id,
                                        'role_id' => $idRoleAppAdmin,
                                        'instance_id' => $dataTransaction->instance_id,
                                        'app_id' => $dataTransaction->app_id,
                                        'confirm_payment' => $validatedData['confirm_payment'],
                                   ];

                                   UserPriviledges::create(
                                        $dataNewPriviledge
                                   );
                              }
                         }
                    } else {
                         $success = false;
                         $errors = 'Transaction ID data not found';
                    }
               } else {
                    $userNames = $dataUsers->pluck('name')->toArray();
                    $success = false;
                    $errors = "Insufficient amount, active users: $userNames you must pay $shouldPay";
               }

               return response()->json([
                    'success' => $success,
                    'message' => $errors ? '' : "Successfully confirm payment",
                    'data' => $data,
                    'errors' => $errors
               ], 200);
          } catch (\Throwable $th) {
               return response()->json([
                    'success' => false,
                    'errors' => $th->getMessage(),
               ], 500);
          }
     }
}