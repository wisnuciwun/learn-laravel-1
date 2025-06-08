<?php
namespace App\Http\Controllers\Api;

use App\Helpers\ItsHelper;
use App\Models\Fianut\Apps;
use App\Models\Fianut\Instances;
use App\Models\Fianut\InstanceSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use App\Models\Fianut\User;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
    public function verifyToken(Request $request)
    {
        $userData = ItsHelper::verifyToken($request->token);

        // Get app by name
        $app = Apps::where('name', $request->app_name)->first();
        if (!$app) {
            return response()->json([
                'success' => false,
                'message' => 'App not found',
            ], 404);
        }

        // Load user with priviledges for that app
        $user = User::with('checkUserPriviledge')
            ->where('id', $userData->id)
            ->first();

        // Filter userPriviledges to find matching app_id
        $hasPriviledge = $user->checkUserPriviledge->firstWhere('app_id', $app->id);

        if ($hasPriviledge) {
            return response()->json([
                'success' => true,
                'message' => 'Token validated & has privilege',
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Token valid but no access to this app',
        ], 403);
    }


    public function googleSignIn(Request $request)
    {
        $request->validate([
            'id_token' => 'required|string',
        ]);

        // 1. Verify id_token using Google's API
        $response = Http::get('https://oauth2.googleapis.com/tokeninfo', [
            'id_token' => $request->id_token,
        ]);

        if ($response->failed() || !$response->json('email')) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or expired Google ID token.',
                'errors' => 'Invalid or expired Google ID token.',
            ], 401);
        }

        $googleUser = $response->json();

        // 2. Get email and name from token
        $email = $googleUser['email'];
        $name = $googleUser['name'] ?? 'Google User';

        // 3. Create or update user
        $user = User::firstOrCreate(
            ['email' => $email],
            ['name' => $name, 'token' => Str::random(60)]
        );

        if (!$user->wasRecentlyCreated) {
            $user->token = Str::random(60); // Refresh token
            $user->name = $name; // Optional: Update name
            $user->save();
        }

        // 4. Return user data and token
        return response()->json([
            'success' => true,
            'message' => 'Signed in with Google successfully.',
            'user' => $user,
            'token' => $user->token,
        ]);
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()->all(),
            ], 422);
        }

        $token = Str::random(60);

        if (!$request->instance_code) {
            $instance_code = ItsHelper::generateInstanceCode($request->instance_name);
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'token' => $token,
            'is_owner' => empty($request->instance_code) ? 1 : 0,
            'instance_code' => !$request->instance_code ? $instance_code : $request->instance_code,
            'nickname' => explode(' ', trim($request->name))[0],
            'active' => 1,
            'gender' => $request->gender,
            'referral_code' => ItsHelper::generateReferralCode($request->name)
        ]);

        if (!$request->instance_code) {
            Instances::create([
                'name' => $request->instance_name,
                'instance_type' => $request->instance_id,
                'instance_code' => $instance_code,
                'user_id' => $user->id
            ])->save();

            InstanceSettings::create([
                'title' => $request->instance_name,
                'instance_code' => $instance_code,
            ])->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'User registered successfully',
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $validator->errors()->all(),
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials',
                'errors' => 'Invalid credentials',
            ], 401);
        }

        // Generate a new token
        $token = Str::random(60);
        $user->token = $token;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function logout(Request $request)
    {
        ItsHelper::verifyToken($request->token);
        $user = User::where('token', $request->token)->first();

        if ($user) {
            $user->update([
                'token' => ''
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Logout successful',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Logout fail',
            ]);
        }
    }

}
