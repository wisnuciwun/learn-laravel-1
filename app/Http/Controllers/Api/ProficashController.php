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
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use App\Models\Fianut\User;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;

class ProficashController extends Controller
{
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


               $totalSales = 0;
               $totalBasePrice = 0;

               foreach ($data as $transaction) {
                    $totalSales += $transaction->price * $transaction->quantity;

                    if ($transaction->inventory) {
                         $totalBasePrice += $transaction->inventory->base_price * $transaction->quantity;
                    }
               }

               return response()->json([
                    'success' => $success,
                    'message' => $errors ? '' : "Successfully saved transaction",
                    'data' => [
                         'transactions' => $data,
                         'highlight' => [
                              'total_sales' => $totalSales,
                              'total_base_price' => $totalBasePrice,
                              'employee_sallary' => 0,
                              'additional_cost' => 0
                         ]
                    ],
               ], $success ? 200 : 400);
          } catch (\Throwable $th) {
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
}