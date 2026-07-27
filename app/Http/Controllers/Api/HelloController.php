<?php
namespace App\Http\Controllers\Api;

use App\Helpers\InstanceHelper;
use App\Helpers\ItsHelper;
use App\Models\Fianut\Apps;
use App\Models\Fianut\Images;
use App\Models\Fianut\Instances;
use App\Models\Fianut\InstanceSettings;
use App\Models\Fianut\Texts;
use App\Models\Fianut\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\Fianut\User;
use Illuminate\Support\Str;
use Carbon\Carbon;
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
                    ->whereNot('phone', 62801234567)
                    ->whereNotNull('phone')
                    ->select('slug', 'title', 'slogan', 'promotion', 'img_heading', 'phone', 'closing_text', 'img_instance_logo')->get();

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

               // If no instance found, return 404
               if (!$dataInstanceSetting) {
                    return response()->json([
                         'success' => false,
                         'message' => 'Instance not found',
                    ], 404);
               }

               // Enforce subscription/privilege expiry: if the Hello app privilege for this instance is expired, deny access
               $app = Apps::where('name', 'Hello')->first();
               $appId = $app ? $app->id : 1; // fallback to 1 if not found
               $priv = \App\Models\Fianut\InstancePriviledges::where('instance_code', $dataInstanceSetting->instance_code)
                    ->where('app_id', $appId)
                    ->first();

               if ($priv && $priv->expired_at) {
                    $expiredAt = Carbon::parse($priv->expired_at);
                    if ($expiredAt->isPast()) {
                         // Subscription expired — deny access to public showcase
                         return response()->json([
                              'success' => false,
                              'message' => 'This page is no longer available (module subscription expired).',
                         ], 403);
                    }
               }

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

     /**
      * Register a custom domain for the Hello app for the current instance.
      * Stores record in settings table with name = 'hello_app_custom_domain' and value = domain name.
      */
     public function registerCustomDomain(Request $request)
     {
          $userData = ItsHelper::verifyToken($request->token);
          $instanceCode = $userData->instance_code;
          $instanceId = $userData->instance->id ?? null;

          $validated = $request->validate([
               'domain' => ['required', 'string', 'max:255', 'regex:/^([a-z0-9]([a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z]{2,}$/i'],
          ]);

          try {
               $domain = trim(strtolower($validated['domain']));

               $existing = Settings::where('name', 'hello_app_custom_domain')
                    ->where('value', $domain)
                    ->where('instance_code', '!=', $instanceCode)
                    ->first();
               if ($existing) {
                    return response()->json([
                         'success' => false,
                         'message' => 'Domain already registered to another instance',
                    ], 422);
               }

               // upsert settings
               $setting = Settings::updateOrCreate(
                    [
                         'name' => 'hello_app_custom_domain',
                         'instance_code' => $instanceCode,
                    ],
                    [
                         'value' => $domain,
                         'instance_id' => $instanceId,
                         'app_id' => Apps::where('name', 'Hello')->value('id') ?? 1,
                    ]
               );

               return response()->json([
                    'success' => true,
                    'message' => 'Custom domain registered',
                    'data' => $setting,
               ], 200);
          } catch (\Throwable $th) {
               return response()->json([
                    'success' => false,
                    'message' => $th->getMessage(),
               ], 500);
          }
     }

     /**
      * Resolve a custom domain to instance info. Public endpoint.
      * GET /hello/custom-domain/resolve?domain=example.com
      */
     public function resolveCustomDomain(Request $request)
     {
          $domain = trim(strtolower($request->query('domain') ?? ''));
          if (!$domain) {
               return response()->json(['success' => false, 'message' => 'Domain required'], 400);
          }
          try {
               $setting = Settings::where('name', 'hello_app_custom_domain')
                    ->where('value', $domain)
                    ->first();

               if (!$setting) {
                    return response()->json(['success' => false, 'message' => 'Not found'], 404);
               }

               $instanceCode = $setting->instance_code;
               $instanceSetting = InstanceSettings::where('instance_code', $instanceCode)->first();
               if (!$instanceSetting) {
                    return response()->json(['success' => false, 'message' => 'Instance not found'], 404);
               }

               // Check privilege not expired (Hello app)
               $appId = Apps::where('name', 'Hello')->value('id') ?? 1;
               $priv = \App\Models\Fianut\InstancePriviledges::where('instance_code', $instanceCode)
                    ->where('app_id', $appId)
                    ->first();
               if ($priv && $priv->expired_at && Carbon::parse($priv->expired_at)->isPast()) {
                    return response()->json(['success' => false, 'message' => 'Domain mapped instance subscription expired'], 403);
               }

               return response()->json([
                    'success' => true,
                    'message' => 'Resolved',
                    'data' => [
                         'instance_code' => $instanceCode,
                         'slug' => $instanceSetting->slug,
                    ],
               ], 200);
          } catch (\Throwable $th) {
               return response()->json(['success' => false, 'message' => $th->getMessage()], 500);
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

               $imagePaths = [];
               if ($request->hasFile('img_closing')) {
                    $existingImage = Images::where('name', 'hello_img_closing')
                         ->where('instance_code', $request->instance_code)
                         ->first();

                    if ($existingImage) {
                         $oldPaths = explode(',', $existingImage->img_path);
                         foreach ($oldPaths as $oldPath) {
                              Storage::delete($oldPath);
                         }
                         $existingImage->delete();
                    }

                    foreach ($request->file('img_closing') as $image) {
                         $path = $image->store('public/fianut/client');
                         $imagePaths[] = $path;
                    }

                    $implodedImagePaths = implode(',', $imagePaths);

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
