<?php
namespace App\Http\Controllers\Api;
use App\Helpers\ItsHelper;
use App\Http\Controllers\Controller;
use App\Models\Fianut\Instances;
use App\Models\Fianut\Inventory;
use Illuminate\Support\Facades\Request;

class InventoryController extends Controller
{
     public function list(Request $request)
     {
          try {
               $instanceId = Instances::where('instance_code', $request->instance_code)->select('id')->first();

               $res = Inventory::when($request->keyword, function ($q) use ($request) {
                    $q->where('name', 'like', "%{$request->keyword}%");
               })
                    ->when($request->sort_by != '', function ($q) use ($request) {
                         $q->orderBy($request->sort_by);
                    })
                    ->when($request->limit != '', function ($q) use ($request) {
                         $q->limit($request->limit);
                    })
                    ->where('instance_code', $instanceId)
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

     public function manage(Request $request)
     {
          $userData = ItsHelper::verifyToken($request->token);
          $request->merge([
               'instance_id' => $userData->instance->id,
          ]);

          $success = true;
          $errors = '';
          $data = [];

          $validatedData = $request->validate([
               'name' => 'required|string|max:255',
               'instance_id' => 'required|string|max:500',
          ]);

          try {
               $dataToSave = [
                    'name' => $validatedData['name'],
                    'description' => $request->description,
                    'price' => $request->price,
                    'base_price' => $request->base_price,
                    'sku' => $request->base_price,
                    'stock' => $request->base_price,
                    'minimum_stock' => $request->base_price,
                    'dummy_stock' => $request->base_price,
                    'promotion_id' => $request->base_price,
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