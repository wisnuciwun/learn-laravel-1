<?php

namespace App\Http\Controllers;

use App\Models\KKIdentity;
use App\Models\PsrNews;
use App\Models\PsrFoods;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Storage;
use Str;

class PagesController extends Controller
{
    // inside here called method

    public function index()
    {
        return 'TRIAL CONTROLLER';
    }

    // get view from pages folder
    public function welcome()
    {
        $connect = 'Or you can visit my LinedIn profile here :';
        $linkedinUrl = 'https://www.linkedin.com/in/wisnu-adi-wardhana-560473163/';
        $data = array(
            // 'subTitle' => 'Hi, here is my website to show up',
            'listPortofolio' => ['E-Pipeline', 'Web Pareto AR', 'Web Todo List', 'Tokodistributor Desktop', 'Scrin', 'SCFS']
        );

        // return view('pages/index'); another options to write
        // return view('pages.index')->with('subTitle', $subTitle); or you can passing a variable like this
        return view('pages.index', compact('connect', 'data', 'linkedinUrl')); // you can add any other value like this
    }

    public function getAllNews()
    {
        try {
            $news = PsrNews::select('id', 'title', 'body', 'author', 'created_at')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $news,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve news.',
            ], 500);
        }
    }

    public function getDetailNews($id)
    {
        try {
            $news = PsrNews::find($id);

            if (!$news) {
                return response()->json([
                    'success' => false,
                    'message' => 'News not found.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $news,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve news details.',
            ], 500);
        }
    }

    public function getAllStores(Request $request)
    {
        try {
            $stores = PsrFoods::select('id', 'store_name', 'owner', 'address', 'phone', 'product_images_url', 'description', 'tags', 'slug')
                ->orderBy('created_at', 'desc')
                ->when($request->keyword, function ($query, $searchKeyword) {
                    $query->where('store_name', 'like', "%$searchKeyword%")->orWhere('tags', 'like', "%$searchKeyword%")->orWhere('owner', 'like', "%$searchKeyword%");
                })
                ->get();

            return response()->json([
                'success' => true,
                'data' => $stores,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getStoreDetail($slug)
    {
        try {
            $store = PsrFoods::where('slug', $slug)->first();
            $store->keypass = isset($store->keypass) ? true : false;

            if (!$store) {
                return response()->json([
                    'success' => false,
                    'message' => 'Store not found.',
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data' => $store,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function postNewStore(Request $request)
    {
        $validatedData = $request->validate([
            'store_name' => 'required|string|max:255',
            'owner' => 'required|string|max:100',
            'address' => 'required|string|max:5',
            'phone' => 'required|string|max:16',
            'product_images' => 'nullable|array',
            'product_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string',
            'tags' => 'nullable|string|max:150',
            'keypass' => 'required|string|max:150',
        ]);

        $imagePaths = [];
        if ($request->hasFile('product_images')) {
            foreach ($request->file('product_images') as $image) {
                $path = $image->store('public/images');
                $imagePaths[] = $path;
            }
        }

        $validatedData['product_images_url'] = implode(',', $imagePaths);

        $slug = Str::slug($validatedData['store_name'], '-');
        $originalSlug = $slug;
        $counter = 1;
        while (DB::table('psr_foods')->where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $validatedData['slug'] = $slug;
        $validatedData['phone'] = $this->phone_format62($request->phone);
        $validatedData['created_at'] = Carbon::now();

        try {
            $store = PsrFoods::create($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Store created successfully.',
                'data' => $store,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the store.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function editStore(Request $request)
    {
        $slug = $request->slug;
        $store = PsrFoods::where('slug', $slug)->first();

        if (!$store) {
            return response()->json([
                'success' => false,
                'message' => 'Store not found.',
            ], 404);
        }

        $validatedData = $request->validate([
            'store_name' => 'required|string|max:255',
            'owner' => 'required|string|max:100',
            'address' => 'required|string|max:5',
            'phone' => 'required|string|max:16',
            'product_images' => 'nullable|array',
            'product_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string',
            'tags' => 'nullable|string|max:150',
            'keypass' => 'nullable|string|max:150',
        ]);

        if (empty($request->keypass)) {
            $validatedData['keypass'] = $store->keypass;
        }

        $imagePaths = [];
        if ($request->hasFile('product_images')) {
            foreach ($request->file('product_images') as $image) {
                $path = $image->store('public/images');
                $imagePaths[] = $path;
            }
            $validatedData['product_images_url'] = implode(',', $imagePaths);
        } else {
            $validatedData['product_images_url'] = $store->product_images_url;
        }

        if ($request->has('store_name') && $store->store_name !== $request->store_name) {
            $slug = Str::slug($validatedData['store_name'], '-');
            $originalSlug = $slug;
            $counter = 1;
            while (DB::table('psr_foods')->where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $counter;
                $counter++;
            }

            $validatedData['slug'] = $slug;
        }

        $validatedData['phone'] = $this->phone_format62($request->phone);

        try {
            $store->update($validatedData);

            return response()->json([
                'success' => true,
                'message' => 'Store updated successfully.',
                'data' => $store,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the store.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function deleteStore($slug)
    {
        $store = PsrFoods::where('slug', $slug)->first();

        if (!$store) {
            return response()->json([
                'success' => false,
                'message' => 'Store not found.',
            ], 404);
        }

        try {
            $store->delete();

            return response()->json([
                'success' => true,
                'message' => 'Store deleted successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while deleting the store.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function checkKeypass(Request $request)
    {
        $validatedData = $request->validate([
            'slug' => 'required|string|max:255',  // Validate slug
            'keypass' => 'required|string|max:255',  // Validate keypass
        ]);

        $store = PsrFoods::where('slug', $validatedData['slug'])->first();

        if (!$store) {
            return response()->json([
                'success' => false,
                'message' => 'Store not found.',
            ], 404);
        }

        if ($store->keypass === $validatedData['keypass']) {
            return response()->json([
                'success' => true,
                'message' => 'Keypass is correct.',
            ], 200);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Incorrect keypass.',
            ], 401); // Unauthorized
        }
    }

    public function postNews(Request $request)
    {
        // Validate incoming data
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string|max:10000',
            'author' => 'required|string|max:255',
        ]);

        // Create the news record in the database
        try {
            $news = PsrNews::create([
                'title' => $validatedData['title'],
                'body' => $validatedData['body'],
                'author' => $validatedData['author'],
            ]);

            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'News created successfully.',
                'data' => $news,
            ], 201);

        } catch (\Exception $e) {
            // Return error response if something goes wrong
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the news.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function uploadKK(Request $request)
    {
        // Validate incoming data
        $validatedData = $request->validate([
            'nama' => 'required|string|max:255',
            'blok' => 'required|string|max:10000',
            'fotoKK' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Create the news record in the database
        try {
            if ($request->hasFile('fotoKK')) {
                $path = $request->file('fotoKK')->store('public/images/01kk');
            }

            $validatedData['kk_path'] = $path;

            $data = KKIdentity::create([
                'nama' => $validatedData['nama'],
                'blok' => $validatedData['blok'],
                'kk_path' => $validatedData['kk_path'],
            ]);

            // Return success response
            return response()->json([
                'success' => true,
                'message' => 'News created successfully.',
                'data' => $data,
            ], 201);

        } catch (\Exception $e) {
            // Return error response if something goes wrong
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the news.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function searchKK(Request $request)
    {
        $query = KKIdentity::query();

        if ($request->has('keyword')) {
            $query->where('nama', 'like', '%' . $request->keyword . '%');
        }

        if ($request->has('blok')) {
            $query->where('blok', 'like', '%' . $request->blok . '%');
        }

        $results = $query->get();

        return response()->json($results);
    }

    public function showImg($imageName)
    {
        $path = storage_path("app/public/images/{$imageName}");
        if (!Storage::exists("public/images/{$imageName}")) {
            abort(404);
        }
        return response()->file($path);
    }

    public function phone_format62($num)
    {
        $result = preg_replace('/[^0-9]/', '', $num);
        return $result[0] === "0" ? "62" . substr($result, 1) : $result;
    }
}