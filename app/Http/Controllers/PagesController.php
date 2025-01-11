<?php

namespace App\Http\Controllers;

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

    public function postNews()
    {

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
            'product_images' => 'nullable|array', // Validate as an array
            'product_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Validate each file
            'description' => 'nullable|string',
            'tags' => 'nullable|string|max:150',
        ]);

        // Handle image upload
        $imagePaths = [];
        if ($request->hasFile('product_images')) {
            foreach ($request->file('product_images') as $image) {
                // Save each image to the 'assets/images' directory
                // $path = $image->store('assets/images', 'public');
                $path = $image->store('public/images');
                $imagePaths[] = $path; // Collect image paths
            }
        }

        $validatedData['product_images_url'] = implode(',', $imagePaths); // Store as a comma-separated string

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

    public function showImg($imageName)
    {
        // Construct the full path to the image within the storage directory
        $path = storage_path("app/public/images/{$imageName}");

        // Check if the image exists; if not, return a 404 response
        if (!Storage::exists("public/images/{$imageName}")) {
            abort(404);
        }

        // Return the image as a response
        return response()->file($path);
    }

    public function phone_format62($num)
    {
        $result = preg_replace('/[^0-9]/', '', $num);
        return $result[0] === "0" ? "62" . substr($result, 1) : $result;
    }
}