<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Services\FileService;
use Illuminate\Support\Facades\Validator;

class ServiceController extends Controller
{
    protected $fileService;
    protected $folderName;
    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
        $this->middleware('auth:api', ['except' => ['getService', 'getServiceDetail']]);
        $this->folderName = 'services';
    }

    public function getService(Request $request)
    {
        $services = Service::orderBy('created_at', 'desc')->get();
        // Decode the descriptions
        $services->transform(function ($service) {
            $service->short_description = htmlspecialchars_decode($service->short_description, ENT_QUOTES);
            $service->long_description = htmlspecialchars_decode($service->long_description, ENT_QUOTES);
            return $service;
        });

        return response([
            'data' => $services
        ]);
    }

    public function getServiceDetail(Request $request) {
        $id = $request->id;
        $service = Service::where('id', $id)->first();

        if ($service) {
            $service->short_description = htmlspecialchars_decode($service->short_description, ENT_QUOTES);
            $service->long_description = htmlspecialchars_decode($service->long_description, ENT_QUOTES);
        }

        return response([
            'data' => $service
        ]);
    }

    public function createService(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string',
                'image' => 'required',
                'alt' => 'string',
                'short_description' => 'string',
                'long_description' => 'string',
            ]);
            if (!$validator->passes()) {
                return response([
                    'message' => 'Invalid Request',
                    'errors' => $validator->errors()
                ], 404);
            }
            $service = new Service();
            $service->title = $request->title;
            $service->alt = $request->alt;
            $service->short_description = htmlspecialchars($request->shortDescription, ENT_QUOTES);
            $service->long_description = htmlspecialchars($request->longDescription, ENT_QUOTES);

            if ($request->hasFile('image')) {
                $uploadedPath = $this->fileService->uploadFile($request->file('image'), $this->folderName);
                $service->image = $uploadedPath;
            }

            $service->save();


            return response([
                'message' => 'Service Added Successfully',
                'data' => $service,
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            return response([
                'status' => false,
                'message' => $th
            ], 500);
        }
    }

    public function updateService(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'title' => 'required|string',
                'image' => 'required',
                'alt' => 'string',
                'short_description' => 'string',
                'long_description' => 'string',
            ]);
            if (!$validator->passes()) {
                return response([
                    'message' => 'Invalid Request',
                    'errors' => $validator->errors()
                ], 404);
            }
            $service = Service::find($request->id);
            $service->title = $request->title;
            $service->alt = $request->alt;
            $service->short_description = htmlspecialchars($request->shortDescription, ENT_QUOTES);
            $service->long_description = htmlspecialchars($request->longDescription, ENT_QUOTES);

            if ($request->hasFile('image')) {
                $this->fileService->deleteFile($service->image, $this->folderName);
                $uploadedPath = $this->fileService->uploadFile($request->file('image'), $this->folderName);
                $service->image = $uploadedPath;
            }
            $service->save();

            return response([
                'message' => 'Service Updated Successfully',
                'data' => $service,
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            return response([
                'status' => false,
                'message' => $th
            ], 500);
        }
    }

    public function deleteService(Request $request)
    {
        try {
            $id = $request->id;
            $image = $request->image;
            Service::where('id', $id)->delete();

            $this->fileService->deleteFile($image, $this->folderName);

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
