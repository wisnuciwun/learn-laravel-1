<?php
namespace App\Http\Controllers\Api;

use App\Helpers\InstanceHelper;
use App\Helpers\ItsHelper;
use App\Models\Fianut\Apps;
use App\Models\Fianut\Images;
use App\Models\Fianut\Instances;
use App\Models\Fianut\InstanceSettings;
use App\Models\Fianut\Texts;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\Fianut\User;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;

class HelloController extends Controller
{
     public function shopList(Request $request)
     {
          try {
               $data = InstanceSettings::
                    when($request->slug != '', function ($q) use ($request) {
                         $q->where('slug', $request->slug);
                    })
                    ->when($request->instance_code != '', function ($q) use ($request) {
                         $q->where('instance_code', $request->instance_code);
                    })
                    ->select('slug', 'instance_code', 'hello_template_id', 'title', 'slogan', 'promotion', 'third_party_links', 'img_heading', 'phone', 'closing_text', 'img_instance_logo')->first();

               return response()->json([
                    'success' => true,
                    'message' => 'Get shop list successful',
                    'data' => $data,
               ], 200);
          } catch (\Throwable $th) {
               return response()->json([
                    'success' => false,
                    'message' => $th->getMessage(),
               ], 500);
          }
     }

     public function showcase(Request $request)
     {
          try {
               $dataInstanceSetting = InstanceSettings::
                    when($request->slug != '', function ($q) use ($request) {
                         $q->where('slug', $request->slug);
                    })
                    ->when($request->instance_code != '', function ($q) use ($request) {
                         $q->where('instance_code', $request->instance_code);
                    })
                    ->select('slug', 'instance_code', 'hello_template_id', 'title', 'slogan', 'promotion', 'third_party_links', 'img_heading', 'phone', 'closing_text', 'img_instance_logo')->first();
               $dataInstance = Instances::where('instance_code', $dataInstanceSetting->instance_code)->select('address')->first();
               $res = Texts::where('name', 'app_hello_template')->where('id', $dataInstanceSetting->hello_template_id)->first();
               $dataImgClosing = ItsHelper::getImages('hello_img_closing', $dataInstanceSetting->instance_code);

               return response()->json([
                    'success' => true,
                    'message' => 'Get hello template list successful',
                    'data' => [
                         'template_html' => $res->data,
                         'instance_settings' => $dataInstanceSetting,
                         'closing_image' => $dataImgClosing,
                         'instance' => $dataInstance
                    ],
               ], 200);
          } catch (\Throwable $th) {
               return response()->json([
                    'success' => false,
                    'message' => $th->getMessage(),
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
                    'message' => $th->getMessage(),
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
               'hello_template_id' => 'required',
               'phone' => 'required'
          ]);

          try {
               $dataToSave = [
                    'title' => $validatedData['title'],
                    'slogan' => $request->slogan,
                    'promotion' => $request->promotion,
                    'third_party_links' => $request->third_party_links,
                    'hello_template_id' => $validatedData['hello_template_id'],
                    'instance_code' => $validatedData['instance_code'],
                    'phone' => $validatedData['phone'],
                    'closing_text' => $request->closing_text,
                    // 'sort_by' => $request->img_heading,
               ];


               $data = InstanceSettings::where('instance_code', $request->instance_code)->first();

               if ($data->title != $validatedData['title']) {
                    $slug = ItsHelper::createSlug($validatedData['title'], 'instance_settings');
                    $dataToSave['slug'] = $slug;
               }

               if (!empty($request->img_heading)) {
                    if (!empty($data->img_heading)) {
                         $image = ItsHelper::saveImage('client', true, $data->img_heading, $request, 'img_heading');
                    } else {
                         $image = ItsHelper::saveImage('client', false, null, $request, 'img_heading');
                    }

                    $dataToSave['img_heading'] = $image;
               }

               // save image bulk
               $imagePaths = [];
               if ($request->hasFile('img_closing')) {
                    // 1. Find existing image record
                    $existingImage = Images::where('name', 'hello_img_closing')
                         ->where('instance_code', $request->instance_code)
                         ->first();

                    // 2. If exists, delete old files from storage
                    if ($existingImage) {
                         $oldPaths = explode(',', $existingImage->img_path);
                         foreach ($oldPaths as $oldPath) {
                              Storage::delete($oldPath);
                         }

                         // Option A: Update the existing record later
                         // Option B: Delete and re-create (you choose one)
                         $existingImage->delete();
                    }

                    // 3. Save new files
                    foreach ($request->file('img_closing') as $image) {
                         $path = $image->store('public/fianut/client');
                         $imagePaths[] = $path;
                    }

                    $implodedImagePaths = implode(',', $imagePaths);

                    // 4. Save the new DB record
                    Images::create([
                         'name' => 'hello_img_closing',
                         'instance_code' => $request->instance_code,
                         'img_path' => $implodedImagePaths,
                    ]);
               }

               if ($data) {
                    $data->update($dataToSave);
               } else {
                    $data = InstanceSettings::create($dataToSave)->save();
               }

               return response()->json([
                    'success' => $success,
                    'message' => $errors ?: "Successfully saved landing page changes",
                    'data' => $data,
               ], $success ? 200 : 400);
          } catch (\Throwable $th) {
               return response()->json([
                    'success' => false,
                    'message' => $th->getMessage(),
               ], 500);
          }
     }

}