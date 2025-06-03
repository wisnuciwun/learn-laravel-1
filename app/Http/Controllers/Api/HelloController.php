<?php
namespace App\Http\Controllers\Api;

use App\Helpers\InstanceHelper;
use App\Helpers\ItsHelper;
use App\Models\Fianut\Apps;
use App\Models\Fianut\Instances;
use App\Models\Fianut\InstanceSettings;
use App\Models\Fianut\Texts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use App\Models\Fianut\User;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;

class HelloController extends Controller
{
     public function showcase(Request $request)
     {
          try {
               $dataInstance = Instances::with(['instanceSetting'])->where('instance_code', $request->instance_code)->first();
               $res = Texts::where('name', 'app_hello_template')->where('id', $dataInstance->instanceSetting->hello_template_id)->get();

               return response()->json([
                    'success' => true,
                    'message' => 'Get hello template list successful',
                    'data' => [
                         'template_html' => $res->data,
                         'instance_data' => $dataInstance
                    ],
               ], 200);
          } catch (\Throwable $th) {
               return response()->json([
                    'success' => false,
                    'errors' => $th->getMessage(),
               ], 500);
          }
     }

     public function templateList(Request $request)
     {
          ItsHelper::verifyToken($request->token);

          try {
               $res = Texts::where('app_id', 1)->when($request->keyword, function ($q) use ($request) {
                    $q->where('name', 'like', "%{$request->keyword}%");
               })->get();

               return response()->json([
                    'success' => true,
                    'message' => 'Get hello template list successful',
                    'data' => $res,
               ], 200);
          } catch (\Throwable $th) {
               return response()->json([
                    'success' => false,
                    'errors' => $th->getMessage(),
               ], 500);
          }
     }

     public function manageLandingPage(Request $request)
     {
          $userData = ItsHelper::verifyToken($request->token);
          $request->merge([
               'instance_code' => $userData->instance->instance_code,
          ]);

          $success = true;
          $errors = '';
          $data = [];

          $validatedData = $request->validate([
               'instance_code' => 'required',
               'title' => 'required|string|max:100',
               'hello_template_id' => 'required'
          ]);

          try {
               $dataToSave = [
                    'title' => $validatedData['title'],
                    'slogan' => $request->slogan,
                    'promotion' => $request->promotion,
                    'third_party_links' => $request->third_party_links,
                    'hello_template_id' => $validatedData['hello_template_id'],
                    // 'sort_by' => $request->img_heading,
               ];

               $data = InstanceSettings::where('instance_code', $request->instance_code)->first();

               if (!empty($request->img_heading)) {
                    if ($data->img_heading) {
                         $image = ItsHelper::saveImage('client', true, $data->image, $request);
                         $dataToSave['img_heading'] = $image;
                    } else {
                         $image = ItsHelper::saveImage('client', false, null, $request);
                         $dataToSave['img_heading'] = $image;
                    }
               }

               if ($data) {
                    $data->update($dataToSave);
               } else {
                    $data = InstanceSettings::create($dataToSave)->save();
               }

               return response()->json([
                    'success' => $success,
                    'message' => $errors ? '' : "Successfully saved landing page changes",
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