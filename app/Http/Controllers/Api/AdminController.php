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
use App\Models\Fianut\Settings;
use App\Models\Fianut\Texts;
use App\Models\Fianut\UserPriviledges;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Models\Fianut\User;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Storage;

class AdminController extends Controller
{
     public function showImgSystem($imageName)
     {
          $path = storage_path("app/public/fianut/system/{$imageName}");
          if (!Storage::exists("public/fianut/system/{$imageName}")) {
               abort(404, "Image not found");
          }
          return response()->file($path);
     }

     public function showImgClient($imageName)
     {
          $path = storage_path("app/public/fianut/client/{$imageName}");
          if (!Storage::exists("public/fianut/client/{$imageName}")) {
               abort(404, "Image not found");
          }
          return response()->file($path);
     }

     public function showImgOther($imageName)
     {
          $path = storage_path("app/public/fianut/other/{$imageName}");
          if (!Storage::exists("public/fianut/other/{$imageName}")) {
               abort(404, "Image not found");
          }
          return response()->file($path);
     }

     public function roles(Request $request)
     {
          $userData = ItsHelper::verifyToken($request->token);
          $request->merge([
               // 'instance_id' => $userData->instance->id,
               'instance_code' => $userData->instance_code,
               'user_id' => $userData->id,
          ]);

          $success = true;
          $errors = '';
          $data = [];

          try {
               $data = Roles::when($request->keyword, function ($q) use ($request) {
                    $q->where('name', 'like', "%{$request->keyword}%");
               })
                    ->when($request->limit, function ($q) use ($request) {
                         $q->limit($request->limit);
                    })
                    ->when($request->sort_by, function ($q) use ($request) {
                         $q->orderBy($request->sort_by);
                    })
                    ->where('instance_code', $request->instance_code)
                    ->orWhereNull('instance_code') // universal roles
                    ->get();

               return response()->json([
                    'success' => $success,
                    'message' => 'Get roles list successfully',
                    'data' => $data,
               ], 200);
          } catch (\Throwable $th) {
               return response()->json([
                    'success' => false,
                    'message' => $th->getMessage(),
               ], 500);
          }
     }

     public function settings(Request $request)
     {
          ItsHelper::verifyToken($request->token);

          $success = true;
          $errors = '';
          $data = [];

          try {
               $data = Settings::
                    when($request->keyword_like, function ($q) use ($request) {
                         $q->where('name', 'like', "%$request->keyword%");
                    })
                    ->when($request->keyword_match, function ($q) use ($request) {
                         $q->where('name', '=', $request->keyword);
                    })
                    ->when($request->app_id, function ($q) use ($request) {
                         $q->where('app_id', '=', $request->app_id);
                    })
                    ->get();

               return response()->json([
                    'success' => $success,
                    'message' => 'Get settings list successfully',
                    'data' => $data,
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
                    'message' => $th->getMessage(),
               ], 500);
          }
     }

     public function appPricings(Request $request)
     {
          try {
               $data = AppPricings::where('app_id', $request->app_id)->get();

               return response()->json([
                    'success' => true,
                    'message' => 'Get app pricing successfully',
                    'data' => $data,
               ], 200);
          } catch (\Throwable $th) {
               return response()->json([
                    'success' => false,
                    'message' => $th->getMessage(),
               ], 500);
          }
     }

     public function manageApp(Request $request)
     {
          ItsHelper::verifyAsAdmin($request->token);
          $success = true;
          $errors = '';
          $data = [];

          $validatedData = $request->validate([
               'name' => 'required|string|max:255',
               'link' => 'required|string|max:500',
               'description' => 'required',
               'short_description' => 'required|string|max:150',
               'image' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
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
                    'message' => $errors ?: "Successfully saved app changes",
                    'data' => $data,
               ], $success ? 200 : 400);
          } catch (\Exception $th) {
               return response()->json([
                    'success' => false,
                    'message' => $th->getMessage(),
               ], 500);
          }
     }

     public function managePricing(Request $request)
     {
          ItsHelper::verifyAsAdmin($request->token);

          $success = true;
          $errors = '';
          $data = [];

          $validatedData = $request->validate([
               'name' => 'required|string|max:100',
               'price' => 'required|integer',
               'member_type' => 'required|integer',
               'app_id' => 'required|integer',
          ]);

          try {
               $dataToSave = [
                    'name' => $validatedData['name'],
                    'price' => $validatedData['price'],
                    'member_type' => $validatedData['member_type'],
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
                    $data = AppPricings::create($dataToSave)->save();
               }

               return response()->json([
                    'success' => $success,
                    'message' => $errors ?: "Successfully saved app pricing changes",
                    'data' => $data,
               ], 200);
          } catch (\Throwable $th) {
               return response()->json([
                    'success' => false,
                    'message' => $th->getMessage(),
               ], 500);
          }
     }

     public function manageProfile(Request $request)
     {
          ItsHelper::verifyToken($request->token);

          $success = true;
          $errors = '';
          $data = [];

          try {
               // filter payload
               $dataToSave = array_filter([
                    'name' => $request->name,
                    'gender' => $request->gender,
                    'nickname' => $request->nickname,
                    'address' => $request->address,
                    'view_type' => $request->view_type,
                    'email_report' => $request->email_report,
                    'target_per_month' => $request->target_per_month,
                    'email' => $request->email,
                    'password' => $request->password,
               ], fn($value) => !is_null($value));

               $data = User::where('id', $request->id)->first();

               if ($data) {
                    if ($request->hasFile('image') && $request->file('image')->isValid()) {
                         if (!empty($data->image)) {
                              $image = ItsHelper::saveImage('other', true, $data->image, $request);
                         } else {
                              $image = ItsHelper::saveImage('other', false, null, $request);
                         }
                         $dataToSave['image'] = $image;
                    }

                    $data->update($dataToSave);
               } else {
                    $success = false;
                    $errors = 'User data not found';
               }

               return response()->json([
                    'success' => $success,
                    'message' => $errors ?: "Successfully saved user data changes",
                    'data' => $data,
               ], 200);
          } catch (\Throwable $th) {
               return response()->json([
                    'success' => false,
                    'message' => $th->getMessage(),
               ], 500);
          }
     }

     // public function manageInstance(Request $request)
     // {
     //      $userData = ItsHelper::verifyToken($request->token);
     //      $request->merge([
     //           'instance_code' => $userData->instance_code,
     //           'user_id' => $userData->id,
     //      ]);

     //      $success = true;
     //      $errors = '';
     //      $data = [];

     //      $validatedData = $request->validate([
     //           'app_id' => 'required',
     //           'instance_id' => 'required',
     //           'user_id' => 'required',
     //           'app_pricings_id' => 'required',
     //      ]);

     //      try {
     //           $dataInstance = Instances::where('id', $request->instance_id)->first();
     //           $dataUser = User::where('id', $request->user_id)->first();
     //           $dataApp = Apps::where('id', $request->app_id)->first();
     //           $dataToSave = [
     //                'app_id' => $validatedData['app_id'],
     //                'instance_id' => $validatedData['instance_id'],
     //                'user_id' => $validatedData['user_id'],
     //                'app_pricings_id' => $validatedData['app_pricings_id'],
     //                'expired_at' => Carbon::now()->addDays(30)
     //           ];

     //           if ($request->id) {
     //                $data = InstancePriviledges::where('id', $request->id)->first();

     //                if ($data && $dataApp && $dataInstance && $dataUser) {
     //                     $data->update($dataToSave);
     //                } else {
     //                     $success = false;
     //                     $errors = 'Required data not found';
     //                }
     //           } else {
     //                if ($dataApp && $dataInstance && $dataUser) {
     //                     $data = InstancePriviledges::create($dataToSave)->save();
     //                } else {
     //                     $success = false;
     //                     $errors = 'Required data not found';
     //                }
     //           }

     //           return response()->json([
     //                'success' => $success,
     //                'message' => $errors ?: "Successfully saved app pricing changes",
     //                'data' => $data,
     //                'errors' => $errors
     //           ], 200);
     //      } catch (\Throwable $th) {
     //           return response()->json([
     //                'success' => false,
     //                'message' => $th->getMessage(),
     //           ], 500);
     //      }
     // }

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
                    'message' => $th->getMessage(),
               ], 500);
          }
     }

     public function manageInstanceTypes(Request $request)
     {
          ItsHelper::verifyAsAdmin($request->token);

          $success = true;
          $errors = '';
          $data = [];

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
                    'message' => $errors ?: "Successfully saved instances type changes",
                    'data' => $data,
               ], $success ? 200 : 400);
          } catch (\Throwable $th) {
               return response()->json([
                    'success' => false,
                    'message' => $th->getMessage(),
               ], 500);
          }
     }

     public function manageTexts(Request $request)
     {
          ItsHelper::verifyToken($request->token);

          $success = true;
          $errors = '';
          $data = [];

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
                    'message' => $errors ?: "Successfully saved texts changes",
                    'data' => $data,
                    'errors' => $errors
               ], $success ? 200 : 400);
          } catch (\Throwable $th) {
               return response()->json([
                    'success' => false,
                    'message' => $th->getMessage(),
               ], 500);
          }
     }

     public function manageSettings(Request $request)
     {
          ItsHelper::verifyToken($request->token);

          $success = true;
          $errors = '';
          $data = [];

          $validatedData = $request->validate([
               'name' => 'required|string|max:255'
          ]);

          try {
               $dataToSave = [
                    'name' => $validatedData['name'],
                    'value' => $request->value,
                    'type' => $request->type,
                    'instance_code' => $request->instance_code,
                    'instance_id' => $request->instance_id,
                    'app_id' => $request->app_id,
                    'user_id' => $request->user_id,
               ];

               if ($request->id) {
                    $data = Settings::where('id', $request->id)->first();

                    if ($data) {
                         $data->update($dataToSave);
                    } else {
                         $success = false;
                         $errors = 'Settings data not found';
                    }
               } else {
                    $data = Settings::create($dataToSave)->save();
               }

               return response()->json([
                    'success' => $success,
                    'message' => $errors ?: "Successfully saved settings changes",
                    'data' => $data,
                    'errors' => $errors
               ], $success ? 200 : 400);
          } catch (\Throwable $th) {
               return response()->json([
                    'success' => false,
                    'message' => $th->getMessage(),
               ], 500);
          }
     }

     public function manageUserPriviledges(Request $request)
     {
          $dataUser = ItsHelper::verifyToken($request->token);
          // $request->merge([
          //      'instance_id' => $userData->instance->id,
          // ]);

          $success = true;
          $errors = '';
          $data = [];

          $validatedData = $request->validate([
               'user_id' => 'required',
               'instance_id' => 'required',
               'role_id' => 'required',
               'app_id' => 'required',
          ]);

          if ($dataUser->is_owner != 1) {
               return response()->json([
                    'success' => false,
                    'message' => "Not allowed to manage user priviledge",
               ], 403);
          }

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

               if ($dataUser && $request->sallary) {
                    $dataUserToSave = [
                         'sallary' => $request->sallary
                    ];

                    $dataUser->update($dataUserToSave);
               }

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
                    'message' => $errors ?: "Successfully saved user priviledge changes",
                    'data' => $data,
               ], $success ? 200 : 400);
          } catch (\Throwable $th) {
               return response()->json([
                    'success' => false,
                    'message' => $th->getMessage(),
               ], 500);
          }
     }

     public function checkPayment(Request $request)
     {
          ItsHelper::verifyToken($request->token);

          try {
               $res = AppPayments::when($request->keyword, function ($q) use ($request) {
                    $q->where('transaction_id', 'like', "%{$request->keyword}%");
               })
                    ->when($request->app_id, function ($q) use ($request) {
                         $q->where($request->app_id);
                    })
                    ->when($request->user_id, function ($q) use ($request) {
                         $q->where($request->user_id);
                    })
                    ->when($request->instance_code, function ($q) use ($request) {
                         $q->where($request->instance_code);
                    })
                    ->get();

               return response()->json([
                    'success' => true,
                    'message' => 'Get payment data successfully',
                    'data' => $res,
               ], 200);
          } catch (\Throwable $th) {
               return response()->json([
                    'success' => false,
                    'message' => $th->getMessage(),
               ], 500);
          }
     }

     public function requestPayment(Request $request)
     {
          $userData = ItsHelper::verifyToken($request->token);
          $request->merge([
               // 'instance_id' => $userData->instance->id,
               'user_id' => $userData->id,
               'instance_code' => $userData->instance_code,
          ]);

          $success = true;
          $errors = '';
          $data = [];

          $validatedData = $request->validate([
               'user_id' => 'required',
               // 'instance_id' => 'required',
               'instance_code' => 'required',
               'app_id' => 'required',
               'app_pricings_id' => 'required',
          ]);

          try {
               // Step 1: Check user access/privilege
               $priv = InstancePriviledges::where('user_id', $request->user_id)
                    ->where('instance_code', $request->instance_code)
                    ->where('app_id', $request->app_id)
                    ->first();

               if (!$priv) {
                    $dataNewInstancePriviledge = [
                         'user_id' => $validatedData['user_id'],
                         // 'instance_id' => $validatedData['instance_id'],
                         'instance_code' => $validatedData['instance_code'],
                         'app_id' => $validatedData['app_id'],
                         'app_pricings_id' => $validatedData['app_pricings_id'],
                         'expired_at' => null,
                    ];

                    InstancePriviledges::create($dataNewInstancePriviledge);
               }

               $now = Carbon::now();

               if (isset($priv->expired_at)) {
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
               }

               // Step 3: Prevent duplicate pending payments
               $exists = AppPayments::where('user_id', $request->user_id)
                    ->where('instance_code', $request->instance_code)
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
               $instanceCode = $request->instance_code ?? 'GEN'; // fallback if instance_code is missing
               $transactionId = ItsHelper::generateTransactionCode($instanceCode);

               // Step 5: Save payment request
               $dataToSave = [
                    'user_id' => $validatedData['user_id'],
                    'instance_code' => $validatedData['instance_code'],
                    'app_id' => $validatedData['app_id'],
                    'app_pricings_id' => $validatedData['app_pricings_id'],
                    'transaction_id' => $transactionId,
                    'confirm_payment' => null,
               ];

               $data = AppPayments::create($dataToSave);

               return response()->json([
                    'success' => $success,
                    'message' => $errors ?: "Successfully saved app pricing changes",
                    'data' => $data,
               ], $success ? 200 : 400);
          } catch (\Throwable $th) {
               return response()->json([
                    'success' => false,
                    'message' => $th->getMessage(),
               ], 500);
          }
     }

     public function confirmPayment(Request $request)
     {
          ItsHelper::verifyAsAdmin($request->token);

          $success = true;
          $errors = '';
          $data = [];

          $validatedData = $request->validate([
               'transaction_id' => 'required',
               'confirm_payment' => 'required',
               'amount' => 'required|integer'
          ]);

          try {
               $dataTransaction = AppPayments::with(['appPricing'])
                    ->where('transaction_id', $request->transaction_id)
                    ->whereNull('confirm_payment')
                    ->latest()
                    ->first();

               $targetMonth = Carbon::now()->subMonth(); // or use Carbon::parse('2025-06-01') for June
               $startOfMonth = $targetMonth->copy()->startOfMonth();
               $endOfMonth = $targetMonth->copy()->endOfMonth();

               $dataUsers = User::withTrashed()
                    ->with([
                         'userPriviledges' => function ($q) use ($dataTransaction) {
                              $q->where('app_id', $dataTransaction->app_id);
                         }
                    ])
                    ->whereHas('userPriviledges', function ($q) use ($dataTransaction) {
                         $q->where('app_id', $dataTransaction->app_id);
                    })
                    ->where('instance_code', $dataTransaction->instance_code)
                    ->where('is_owner', '!=', 1)
                    ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                    ->whereBetween('deleted_at', [$startOfMonth, $endOfMonth])
                    ->select('id', 'name')
                    ->get();

               $dataToSave = [
                    'confirm_payment' => $validatedData['confirm_payment'],
               ];

               $userCount = (clone $dataUsers)->count();
               $price = optional($dataTransaction->appPricing)->price ?? 0;
               $shouldPay = $price * ($userCount != 0 ? $userCount : 1);

               if ($request->amount == $shouldPay) {
                    if ($dataTransaction) {
                         if ($validatedData['confirm_payment'] == 1) {
                              $dataInstancePriviledgesToSave = [
                                   'expired_at' => Carbon::parse($dataInstancePriviledges->expired_at ?? now())->addDays(30)
                              ];
                              $dataInstancePriviledges = InstancePriviledges::
                                   where('instance_code', $dataTransaction->instance_code)
                                   ->where('app_id', $dataTransaction->app_id)
                                   ->get();
                              $dataInstancePriviledges->each(function ($item) use ($dataInstancePriviledgesToSave) {
                                   $item->update($dataInstancePriviledgesToSave);
                              });

                              // referral poin system
                              $dataOwnerUser = User::select('name', 'referred_by')
                                   ->where('id', $dataTransaction->user_id)
                                   ->whereNotNull('referred_by')
                                   ->first();

                              if ($dataOwnerUser) {
                                   $dataReferral = User::select('name', 'poins')
                                        ->where('referral_code', $dataOwnerUser->referred_by)
                                        ->first();

                                   if ($dataReferral) {
                                        $dataReferral->increment('poins', $shouldPay * 0.1); // Make sure to specify the column name here
                                   }
                              }

                              $dataTransaction->update($dataToSave);

                              $isAlreadyPriviledged = UserPriviledges::where('user_id', $dataTransaction->user_id)->where('app_id', $dataTransaction->app_id)->first();
                              $idRoleAppAdmin = Roles::where('name', 'app_admin')->first();

                              if (!$isAlreadyPriviledged) {
                                   $dataInstance = Instances::where('instance_code', $dataTransaction->instance_code)->first();

                                   $dataNewPriviledge = [
                                        'user_id' => $dataTransaction->user_id,
                                        'role_id' => $idRoleAppAdmin->id,
                                        'instance_id' => $dataInstance->id,
                                        'app_id' => $dataTransaction->app_id,
                                   ];

                                   UserPriviledges::create(
                                        $dataNewPriviledge
                                   );
                              }
                         } else {
                              $success = true;
                              $errors = 'Transaction has been canceled';
                         }
                    } else {
                         $success = false;
                         $errors = 'Transaction ID data not found or has been confirmed';
                    }
               } else {
                    // $userNames = $dataUsers->pluck('name')->toArray();
                    $success = false;
                    $errors = "Insufficient amount, active users is more than 1. You must pay $shouldPay";
               }

               return response()->json([
                    'success' => $success,
                    'message' => $errors ?: "Successfully confirm payment",
                    'data' => $data,
               ], $success ? 200 : 400);
          } catch (\Throwable $th) {
               return response()->json([
                    'success' => false,
                    'message' => $th->getMessage(),
               ], 500);
          }
     }

     public function manageRole(Request $request)
     {
          $userData = ItsHelper::verifyToken($request->token);
          if ($userData->name != '8uset9w4dmin') {
               $request->merge([
                    'instance_id' => $userData->instance->id,
                    'instance_code' => $userData->instance_code,
                    'user_id' => $userData->id,
               ]);
          }

          $success = true;
          $errors = '';
          $data = [];

          $validatedData = $request->validate([
               'name' => 'required|string|max:100',
          ]);

          try {
               $dataToSave = [
                    'instance_code' => $request->instance_code,
                    'tabs' => $request->tabs,
                    'description' => $request->description,
                    'name' => $validatedData['name']
               ];

               if ($request->id) {
                    $data = Roles::where('id', $request->id)->first();

                    if ($data) {
                         $data->update($dataToSave);
                    } else {
                         $success = false;
                         $errors = 'Role data not found';
                    }
               } else {
                    $data = Roles::create($dataToSave)->save();
               }

               return response()->json([
                    'success' => $success,
                    'message' => $errors ?: "Successfully saved role changes",
                    'data' => $data,
               ], $success ? 200 : 400);
          } catch (\Throwable $th) {
               return response()->json([
                    'success' => false,
                    'message' => $th->getMessage(),
               ], 500);
          }
     }

     public function deletePriviledge(Request $request)
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
                    $priviledge = UserPriviledges::whereIn('id', $validatedData['id'])->get();

                    if ($priviledge->isEmpty()) {
                         $success = false;
                         $errors = 'No priviledge found to delete';
                    } else {
                         $data = $priviledge->toArray();
                         UserPriviledges::whereIn('id', $priviledge->pluck('id'))->delete();
                    }
               } else {
                    $success = false;
                    $errors = 'User not allowed';
               }

               return response()->json([
                    'success' => $success,
                    'message' => $errors ?: "Successfully delete role",
                    'data' => $data,
               ], $success ? 200 : 400);
          } catch (\Exception $th) {
               return response()->json([
                    'success' => false,
                    'message' => $th->getMessage(),
               ], 500);
          }
     }

     public function deleteRole(Request $request)
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
                    $roles = Roles::whereIn('id', $validatedData['id'])
                         ->where('instance_code', $request->instance_code)
                         ->get();

                    if ($roles->isEmpty()) {
                         $success = false;
                         $errors = 'No roles found to delete';
                    } else {
                         $data = $roles->toArray();
                         Roles::whereIn('id', $roles->pluck('id'))->delete();
                    }
               } else {
                    $success = false;
                    $errors = 'User not allowed';
               }

               return response()->json([
                    'success' => $success,
                    'message' => $errors ?: "Successfully delete role",
                    'data' => $data,
               ], $success ? 200 : 400);
          } catch (\Exception $th) {
               return response()->json([
                    'success' => false,
                    'message' => $th->getMessage(),
               ], 500);
          }
     }

     public function deleteUser(Request $request)
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
               'id' => 'required',
          ]);

          try {
               if ($userData->is_owner == 1) {
                    $data = User::where('id', $validatedData['id'])->where('instance_code', $request->instance_code)->first();

                    if ($data) {
                         $data->delete();
                    } else {
                         $success = false;
                         $errors = 'User data not found';
                    }
               } else {
                    $success = false;
                    $errors = 'User not allowed';
               }


               return response()->json([
                    'success' => $success,
                    'message' => $errors ?: "Successfully delete user",
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