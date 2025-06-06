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
          $request->merge([
               'instance_id' => $userData->instance->id,
               'user_id' => $userData->id,
          ]);

          $success = true;
          $errors = '';
          $data = [];

          $validatedData = $request->validate([
               'instance_id' => 'required',
               'user_id' => 'required',
               'inventory_id' => 'required',
               'price' => 'required|integer',
               'quantity' => 'required|integer',
          ]);

          try {
               $dataToCreate = [
                    'instance_id' => $validatedData['instance_id'],
                    'price' => $validatedData['price'],
                    'quantity' => $validatedData['quantity'],
                    'user_id' => $validatedData['user_id'],
                    'inventory_id' => $validatedData['inventory_id']
               ];
               $dataInstance = Instances::where('id', $request->instance_id)->first();
               $dataInventory = Inventory::where('id', $request->inventory_id)->first();

               if ($dataInstance && $dataInventory) {
                    $data = TransactionsIn::insert($dataToCreate);
               } else {
                    $success = false;
                    $errors = 'Required data not found';
               }

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