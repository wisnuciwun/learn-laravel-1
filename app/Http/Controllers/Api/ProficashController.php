<?php
namespace App\Http\Controllers\Api;

use App\Helpers\ItsHelper;
use App\Models\Fianut\AppPricings;
use App\Models\Fianut\Apps;
use App\Models\Fianut\InstancePriviledges;
use App\Models\Fianut\Instances;
use App\Models\Fianut\InstanceTypes;
use App\Models\Fianut\Inventory;
use App\Models\Fianut\Texts;
use App\Models\Fianut\TransactionsIn;
use App\Models\Fianut\TransactionsOut;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\Fianut\User;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;

class ProficashController extends Controller
{
     public function summary(Request $request)
     {
          $userData = ItsHelper::verifyToken($request->token);
          $request->merge([
               // 'instance_id' => $userData->instance->id,
               'user_id' => $userData->id,
          ]);

          $success = true;
          $errors = '';
          $data = [];

          try {
               $dataUser = User::select('name', 'sallary', 'instance_code')->where('instance_code', $request->instance_code)->where('is_owner', 0)->get();
               $dataTransactionIn = TransactionsIn::with('inventory:id,base_price,operational_price')->select('id', 'inventory_id', 'price', 'quantity')
                    ->whereIn('instance_id', $userData->instance->pluck('id')->toArray())
                    ->when($request->start_date && $request->end_date, function ($q) use ($request) {
                         $q->whereBetween('created_at', [
                              $request->start_date . " 00:00:00",
                              $request->end_date . ' 23:59:59'
                         ]);
                    })
                    ->when(!$request->start_date && !$request->end_date, function ($q) {
                         $q->whereBetween('created_at', [
                              Carbon::now()->firstOfMonth(),
                              Carbon::now()->endOfMonth()
                         ]);
                    })
                    ->get();
               $dataTransactionOut = TransactionsOut::select('price', 'quantity', 'instance_id')
                    ->whereIn('instance_id', $userData->instance->pluck('id')->toArray())
                    ->when($request->start_date && $request->end_date, function ($q) use ($request) {
                         $q->whereBetween('created_at', [
                              $request->start_date . " 00:00:00",
                              $request->end_date . ' 23:59:59'
                         ]);
                    })
                    ->when(!$request->start_date && !$request->end_date, function ($q) {
                         $q->whereBetween('created_at', [
                              Carbon::now()->firstOfMonth(),
                              Carbon::now()->endOfMonth()
                         ]);
                    })
                    ->get();

               $sales = $dataTransactionIn->sum(function ($item) {
                    return $item->price * $item->quantity;
               });
               $modal = $dataTransactionIn->sum(function ($item) {
                    return optional($item->inventory)->base_price + optional($item->inventory)->operational_price;
               });
               $profit = $sales - $modal;

               $data = [
                    'total_sales' => $sales,
                    'total_modal' => $modal,
                    'total_spending' => $dataTransactionOut->sum(function ($item) {
                         return $item->price * $item->quantity;
                    }),
                    'employee_sallary' => $dataUser,
                    'total_sold_items' => $dataTransactionIn->sum('quantity'),
                    'total_profit' => $profit,
                    'profit_percentage' => number_format($userData->target_per_month
                         ? ($profit / $userData->target_per_month) * 100
                         : 0, 2),
                    'target_per_month' => $userData->target_per_month
               ];

               return response()->json([
                    'success' => $success,
                    'message' => $errors ?: "Successfully get common outcome items",
                    'data' => $data,
               ], 200);
          } catch (\Throwable $th) {
               return response()->json([
                    'success' => false,
                    'message' => $th->getMessage(),
               ], 500);
          }
     }

     public function commonSpendingItems(Request $request)
     {
          $userData = ItsHelper::verifyToken($request->token);
          $request->merge([
               'instance_id' => $userData->instance->id,
               'user_id' => $userData->id,
          ]);

          $success = true;
          $errors = '';
          $data = [];

          try {
               $data = TransactionsOut::select('name')
                    ->where('instance_id', $request->instance_id)
                    ->groupBy('name')->get();

               return response()->json([
                    'success' => $success,
                    'message' => $errors ?: "Successfully get common outcome items",
                    'data' => $data,
               ], 200);
          } catch (\Throwable $th) {
               return response()->json([
                    'success' => false,
                    'message' => $th->getMessage(),
               ], 500);
          }
     }

     public function spending(Request $request)
     {
          $userData = ItsHelper::verifyToken($request->token);
          $request->merge([
               'instance_id' => $userData->instance->id,
               'user_id' => $userData->id,
          ]);

          $success = true;
          $errors = '';
          $data = [];

          try {
               $data = TransactionsOut::select('name', 'price', 'quantity', 'created_at', 'transaction_code', 'payment_method')
                    ->where('instance_id', $request->instance_id)
                    ->when($request->start_date && $request->end_date, function ($q) use ($request) {
                         $q->whereBetween('created_at', [
                              $request->start_date . " 00:00:00",
                              $request->end_date . ' 23:59:59'
                         ]);
                    })
                    ->when(!$request->start_date && !$request->end_date, function ($q) {
                         $q->whereBetween('created_at', [
                              Carbon::now()->firstOfMonth(),
                              Carbon::now()->endOfMonth()
                         ]);
                    })
                    ->get();

               return response()->json([
                    'success' => $success,
                    'message' => $errors ?: "Successfully get spending transaction",
                    'data' => $data,
               ], $success ? 200 : 400);
          } catch (\Throwable $th) {
               return response()->json([
                    'success' => false,
                    'message' => $th->getMessage(),
               ], 500);
          }
     }

     public function transactions(Request $request)
     {
          $userData = ItsHelper::verifyToken($request->token);
          $request->merge([
               'instance_id' => $userData->instance->id,
               'user_id' => $userData->id,
          ]);

          $success = true;
          $errors = '';
          $data = [];

          try {
               if ($userData->is_owner == 1) {
                    $data = TransactionsIn::with('inventory:id,name,image,price,base_price')
                         ->select('price', 'quantity', 'inventory_id', 'instance_id', 'created_at', 'transaction_code', 'payment_method')
                         ->whereIn('instance_id', $userData->instance->pluck('id')->toArray())
                         ->when($request->start_date && $request->end_date, function ($q) use ($request) {
                              $q->whereBetween('created_at', [
                                   $request->start_date . " 00:00:00",
                                   $request->end_date . ' 23:59:59'
                              ]);
                         })
                         ->when(!$request->start_date && !$request->end_date, function ($q) {
                              $q->whereBetween('created_at', [
                                   Carbon::now()->firstOfMonth(),
                                   Carbon::now()->endOfMonth()
                              ]);
                         })
                         ->get();
               } else {
                    $data = TransactionsIn::with('inventory:id,name,image,price,base_price')
                         ->select('price', 'quantity', 'inventory_id', 'created_at', 'transaction_code', 'payment_method')
                         ->where('instance_id', $request->instance_id)
                         ->when($request->start_date && $request->end_date, function ($q) use ($request) {
                              $q->whereBetween('created_at', [
                                   $request->start_date . " 00:00:00",
                                   $request->end_date . ' 23:59:59'
                              ]);
                         })
                         ->when(!$request->start_date && !$request->end_date, function ($q) {
                              $q->whereBetween('created_at', [
                                   Carbon::now()->firstOfMonth(),
                                   Carbon::now()->endOfMonth()
                              ]);
                         })
                         ->get();
               }

               return response()->json([
                    'success' => $success,
                    'message' => $errors ?: "Successfully get transaction",
                    'data' => $data,
               ], $success ? 200 : 400);
          } catch (\Throwable $th) {
               return response()->json([
                    'success' => false,
                    'message' => $th->getMessage(),
               ], 500);
          }
     }

     public function addTransactionOut(Request $request)
     {
          $userData = ItsHelper::verifyToken($request->token);
          $request->merge([
               'instance_id' => $userData->instance->id,
               'user_id' => $userData->id,
               'instance_code' => $userData->instance_code,
          ]);

          $transactionCode = ItsHelper::generateTransactionCode($userData->instance->instance_code, true);
          $validated = $request->validate([
               'transactions' => 'required|array',
               'transactions.*.name' => 'required|string',
               'transactions.*.price' => 'required|integer',
               'transactions.*.quantity' => 'required|integer',
          ]);

          $dataToInsert = collect($validated['transactions'])->map(function ($item) use ($request, $transactionCode) {
               return [
                    'name' => $item['name'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'user_id' => $request->user_id,
                    'instance_id' => $request->instance_id,
                    'transaction_code' => $transactionCode,
                    'payment_method' => $request->payment_method,
                    'created_at' => now(),
                    'updated_at' => now(),
               ];
          })->toArray();

          $dataTextToSave = [
               'title' => $request->receipt_id,
               'name' => $transactionCode,
               'instance_id' => $request->instance_id
          ];

          try {
               TransactionsOut::insert($dataToInsert);
               if (!empty($request->image)) {
                    $image = ItsHelper::saveImage('client', false, null, $request);
                    $dataTextToSave['data'] = $image;
               }
               Texts::create($dataTextToSave)->save();

               return response()->json([
                    'success' => true,
                    'message' => 'Successfully saved outcome transaction',
                    'data' => $dataToInsert,
               ], 200);
          } catch (\Exception $th) {
               return response()->json([
                    'success' => false,
                    'message' => $th->getMessage(),
               ], 500);
          }
     }

     public function addTransactionIn(Request $request)
     {
          $userData = ItsHelper::verifyToken($request->token);
          $transactionCode = ItsHelper::generateTransactionCode($userData->instance->instance_code, true);

          $validated = $request->validate([
               'transactions' => 'required|array',
               'transactions.*.inventory_id' => 'required|integer',
               'transactions.*.price' => 'required|integer',
               'transactions.*.quantity' => 'required|integer',
               'transactions.*.payment_method' => 'required|string|max:15',
          ]);

          $dataToInsert = collect($validated['transactions'])->map(function ($item) use ($userData, $transactionCode) {
               return [
                    'instance_id' => $userData->instance->id,
                    'transaction_code' => $transactionCode,
                    'user_id' => $userData->id,
                    'inventory_id' => $item['inventory_id'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'payment_method' => $item['payment_method'],
                    'created_at' => now(),
                    'updated_at' => now(),
               ];
          })->toArray();

          try {
               TransactionsIn::insert($dataToInsert);

               return response()->json([
                    'success' => true,
                    'message' => 'Successfully saved transactions',
                    'data' => $dataToInsert,
               ], 200);
          } catch (\Exception $th) {
               return response()->json([
                    'success' => false,
                    'message' => $th->getMessage(),
               ], 500);
          }
     }

     public function deleteTransactionIn(Request $request)
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
               'transaction_code' => 'required|array',
               'transaction_code.*' => 'string'
          ]);

          try {
               $transactions = TransactionsIn::whereIn('transaction_code', $validatedData['transaction_code'])->get();

               if ($transactions->isEmpty()) {
                    $success = false;
                    $errors = 'No transaction found to delete';
               } else {
                    $data = $transactions->toArray();
                    TransactionsIn::whereIn('transaction_code', $transactions->pluck('transaction_code'))->delete();
               }

               return response()->json([
                    'success' => $success,
                    'message' => $errors ?: "Successfully delete transaction",
                    'data' => $data,
               ], $success ? 200 : 400);
          } catch (\Exception $th) {
               return response()->json([
                    'success' => false,
                    'message' => $th->getMessage(),
               ], 500);
          }
     }

     public function deleteTransactionOut(Request $request)
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
               'transaction_code' => 'required|array',
               'transaction_code.*' => 'string'
          ]);

          try {
               $transactions = TransactionsOut::whereIn('transaction_code', $validatedData['transaction_code'])->get();

               if ($transactions->isEmpty()) {
                    $success = false;
                    $errors = 'No outcome transaction found to delete';
               } else {
                    $data = $transactions->toArray();
                    TransactionsOut::whereIn('transaction_code', $transactions->pluck('transaction_code'))->delete();
                    $dataText = Texts::where('name', $validatedData['transaction_code'])->first();

                    Storage::delete($dataText->data);
                    $dataText->delete();
               }

               return response()->json([
                    'success' => $success,
                    'message' => $errors ?: "Successfully delete transaction",
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