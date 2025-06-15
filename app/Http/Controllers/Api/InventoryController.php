<?php
namespace App\Http\Controllers\Api;
use App\Helpers\ItsHelper;
use App\Http\Controllers\Controller;
use App\Models\Fianut\Instances;
use App\Models\Fianut\Inventory;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
     public function detail(Request $request)
     {
          try {
               $res = Inventory::where('id', $request->id)->first();

               return response()->json([
                    'success' => true,
                    'message' => 'Get inventory successfully',
                    'data' => $res,
               ], 200);
          } catch (\Throwable $th) {
               return response()->json([
                    'success' => false,
                    'errors' => $th->getMessage(),
               ], 500);
          }
     }

     public function list(Request $request)
     {
          try {
               $res = Inventory::when($request->keyword, function ($q) use ($request) {
                    $q->where('name', 'like', "%{$request->keyword}%");
               })
                    ->when($request->sort_by != '', function ($q) use ($request) {
                         $q->orderBy($request->sort_by);
                    })
                    ->when($request->limit != '', function ($q) use ($request) {
                         $q->limit($request->limit);
                    })
                    ->where('instance_code', $request->instance_code)
                    ->get();

               return response()->json([
                    'success' => true,
                    'message' => 'Get all inventory list successfully',
                    'data' => $res,
               ], 200);
          } catch (\Throwable $th) {
               return response()->json([
                    'success' => false,
                    'errors' => $th->getMessage(),
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
                    $inventories = Inventory::whereIn('id', $validatedData['id'])
                         ->where('instance_code', $request->instance_code)
                         ->get();

                    if ($inventories->isEmpty()) {
                         $success = false;
                         $errors = 'No inventory found to delete.';
                    } else {
                         $data = $inventories->toArray();
                         Inventory::whereIn('id', $inventories->pluck('id'))->delete();
                    }

               } else {
                    $success = false;
                    $errors = 'User not allowed';
               }


               return response()->json([
                    'success' => $success,
                    'message' => $errors ? '' : "Successfully delete inventory",
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

     public function manage(Request $request)
     {
          $userData = ItsHelper::verifyToken($request->token);
          $request->merge([
               'instance_code' => $userData->instance->instance_code,
               'instance_id' => $userData->instance->id,
          ]);

          $success = true;
          $errors = '';
          $data = [];

          $validatedData = $request->validate([
               'name' => 'required|string|max:255',
               'instance_code' => 'required',
               'instance_id' => 'required',
          ]);

          try {
               $dataToSave = [
                    'name' => $validatedData['name'],
                    'instance_code' => $validatedData['instance_code'],
                    'instance_id' => $validatedData['instance_id'],
                    'description' => $request->description,
                    'price' => $request->price,
                    'base_price' => $request->base_price,
                    'sku' => $request->sku,
                    'stock' => $request->stock,
                    'minimum_stock' => $request->minimum_stock,
                    'dummy_stock' => $request->dummy_stock,
                    'promotion_id' => $request->promotion_id,
               ];

               if ($request->id) {
                    $data = Inventory::where('id', $request->id)->first();

                    if ($data) {
                         if (!empty($request->image)) {
                              $image = ItsHelper::saveImage('client', true, $data->image, $request);
                              $dataToSave['image'] = $image;
                         }

                         $data->update($dataToSave);
                    } else {
                         $success = false;
                         $errors = 'Inventory data not found';
                    }
               } else {
                    if (!empty($request->image)) {
                         $image = ItsHelper::saveImage('client', false, null, $request);
                         $dataToSave['image'] = $image;
                    }

                    $data = Inventory::create($dataToSave)->save();
               }

               return response()->json([
                    'success' => $success,
                    'message' => $errors ? '' : "Successfully saved inventory changes",
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