<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\About;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AboutController extends Controller
{
    protected $fileService;
    protected $folderName;
    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
        $this->middleware('auth:api', ['except' => ['getAbout']]);
        $this->folderName = 'about';
    }

    public function getAbout()
    {
        $abouts = About::orderBy('created_at', 'desc')->first();
        // Decode the descriptions
        if ($abouts) {
            $abouts->short_description = htmlspecialchars_decode($abouts->short_description, ENT_QUOTES);
            $abouts->long_description = htmlspecialchars_decode($abouts->long_description, ENT_QUOTES);
        }

        return response([
            'data' => $abouts
        ]);
    }

    public function createAbout(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string',
                'title_badge' => 'required|string',
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp',
                'alt' => 'string|nullable',
                'short_description' => 'string',
                'long_description' => 'nullable|string',
            ]);
            if (!$validator->passes()) {
                return response([
                    'message' => 'Invalid Request',
                    'errors' => $validator->errors()
                ], 404);
            }
            $about = new About();
            $about->title = $request->title;
            $about->title_badge = $request->title_badge;
            $about->alt = $request->alt;
            $about->short_description = htmlspecialchars($request->short_description, ENT_QUOTES);
            $about->long_description = htmlspecialchars($request->long_description, ENT_QUOTES);

            if ($request->hasFile('image')) {
                $uploadedPath = $this->fileService->uploadFile($request->file('image'), $this->folderName);
                $about->image = $uploadedPath;
            }

            $about->save();

            return response([
                'message' => 'About Added Successfully',
                'data' => $about,
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            return response([
                'status' => false,
                'message' => $th
            ], 500);
        }
    }

    public function updateAbout(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'id' => 'required|string',
            'title' => 'required|string',
            'title_badge' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg',
            'alt' => 'nullable|string',
            'short_description' => 'nullable|string',
            'long_description' => 'nullable|string',
        ]);
        if (!$validator->passes()) {
            return response([
                'message' => 'Invalid Request',
                'errors' => $validator->errors(),
                'request' => $request->id
            ], 404);
        }
        
        try {
            $about = About::find($request->id);
            $about->title = $request->title;
            $about->title_badge = $request->title_badge;
            $about->alt = $request->alt;
            $about->short_description = htmlspecialchars($request->short_description, ENT_QUOTES);
            $about->long_description = htmlspecialchars($request->long_description, ENT_QUOTES);

            if ($request->hasFile('image')) {
                //delete old image
                $this->fileService->deleteFile($about->image, $this->folderName);
                $uploadedPath = $this->fileService->uploadFile($request->file('image'), $this->folderName);
                $about->image = $uploadedPath;
            }

            $about->save();

            return response([
                'message' => 'About Updated Successfully',
                'data' => $about,
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            return response([
                'status' => false,
                'message' => $th
            ], 500);
        }
    }
}
