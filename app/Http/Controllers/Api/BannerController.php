<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BannerController extends Controller
{
    protected $fileService;
    protected $folderName;
    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
        $this->middleware('auth:api', ['except' => ['getBanner']]);
        $this->folderName = 'banners';
    }

    public function getBanner(request $request)
    {
        $banners = Banner::orderBy('created_at', 'desc')->get();

        return response([
            'data' => $banners
        ]);
    }

    public function createBanner(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
                'alt' => 'nullable|string',
            ]);
            if (!$validator->passes()) {
                return response([
                    'message' => 'Invalid Request',
                    'errors' => $validator->errors()
                ], 404);
            }
            $banner = new Banner();
            $banner->alt = $request->alt;

            if ($request->hasFile('image')) {
                $uploadedPath = $this->fileService->uploadFile($request->file('image'), $this->folderName);
                $banner->image = $uploadedPath;
            }

            $banner->save();


            return response([
                'message' => 'Partner Added Successfully',
                'data' => $banner,
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            return response([
                'status' => false,
                'message' => $th
            ], 500);
        }
    }

    public function deleteBanner(Request $request)
    {
        try {
            $id = $request->id;
            Banner::where('id', $id)->delete();

            if ($request->image) {
                $this->fileService->deleteFile($request->image, $this->folderName);
            }

            return response([
                'message' => 'Service Deleted Successfully'
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
