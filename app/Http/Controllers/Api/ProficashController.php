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
     public function getTransactions(Request $request)
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
               $data = TransactionsIn::with('inventory:name,image,price,base_price')
                    ->where('instance_id', $request->instance_id)
                    ->when($request->start_date != '' && $request->end_date != '', function ($q) use ($request) {
                         $q->whereBetween('created_at', [$request->start_date . " 00:00:00", $request->end_date . ' 23:59:59']);
                    })
                    ->when($request->start_date == '' && $request->end_date == '', function ($q) {
                         $q->whereBetween('created_at', [Carbon::now()->firstOfMonth(), Carbon::now()->endOfMonth()]);
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
                         'total_sales' => $totalSales,
                         'total_base_price' => $totalBasePrice,
                         'employee_sallary' => 0,
                         'additional_cost' => 0
                    ],
                    'errors' => $errors
               ], 200);
          } catch (\Throwable $th) {
               return response()->json([
                    'success' => false,
                    'errors' => $th->getMessage(),
               ], 500);
          }

     }

     public function addTransactionIn(Request $request)
     {
          $userData = ItsHelper::verifyToken($request->token);

          $success = true;
          $errors = '';
          $data = [];

          $validated = $request->validate([
               'transactions' => 'required|array',
               'transactions.*.inventory_id' => 'required|integer',
               'transactions.*.price' => 'required|integer',
               'transactions.*.quantity' => 'required|integer',
          ]);

          $dataToInsert = collect($validated['transactions'])->map(function ($item) use ($userData) {
               return [
                    'inventory_id' => $item['inventory_id'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
               ];
          })->toArray();

          try {
               $dataToCreate = [
                    'instance_id' => $userData->instance->id,
                    'user_id' => $userData->id,
                    'price' => $dataToInsert['price'],
                    'quantity' => $dataToInsert['quantity'],
                    'inventory_id' => $dataToInsert['inventory_id']
               ];
               $data = TransactionsIn::insert($dataToCreate);

               return response()->json([
                    'success' => $success,
                    'message' => $errors ? '' : "Successfully saved transaction",
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

}