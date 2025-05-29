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
     public function addTransactionIn(Request $request)
     {
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

                    $data = TransactionsIn::create($dataToCreate)->save();
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