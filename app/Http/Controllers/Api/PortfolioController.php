<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Portfolio;
use App\Services\FileService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PortfolioController extends Controller
{
    protected $fileService;
    protected $folderName;
    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
        $this->middleware('auth:api', ['except' => ['getPortfolio']]);
        $this->folderName = 'portfolio';
    }
    public function getPortfolio(request $request)
    {
        $pageSize = 9;
        if ($request->has('page_size')) {
            $pageSize = $request->page_size;
        }
        $portfolio = Portfolio::orderBy('created_at', 'desc')->paginate($pageSize);

        return response([
            'data' => $portfolio
        ]);
    }

    public function createPortfolio(Request $request)
    {
        try {
            $portfolio = new Portfolio();
            $portfolio->alt = $request->alt;
            $portfolio->title = $request->title;
            $portfolio->description = $request->description;
            $validator = Validator::make($request->all(), [
                'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
                'alt' => 'nullable|string',
                'title' => 'nullable|string',
                'description' => 'nullable|string',
            ]);

            if (!$validator->passes()) {
                return response([
                    'message' => "Invalid Request",
                    'errors' => $validator->errors()
                ], 404);
            }


            if ($request->hasFile('image')) {
                $uploadedPath = $this->fileService->uploadFile($request->file('image'), $this->folderName);
                $portfolio->image = $uploadedPath;
            }

            $portfolio->save();


            return response([
                'message' => 'Portfolio Added Successfully',
                'data' => $portfolio,
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            return response([
                'status' => false,
                'error' => $th->getMessage(), // Include the error message
                'trace' => config('app.debug') ? $th->getTrace() : null, // Include trace only in debug mode
            ], 500);
        }
    }

    public function updatePortfolio(Request $request)
    {
        try {
            $portfolio = Portfolio::find($request->id);
            $portfolio->alt = $request->alt;
            $portfolio->title = $request->title;
            $portfolio->description = $request->description;
            $validator = Validator::make($request->all(), [
                'id' => 'required|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
                'alt' => 'nullable|string',
                'title' => 'nullable|string',
                'description' => 'nullable|string',
            ]);

            if (!$validator->passes()) {
                return response([
                    'message' => "Invalid Request",
                    'errors' => $validator->errors()
                ], 404);
            }


            if ($request->hasFile('image')) {
                $this->fileService->deleteFile($portfolio->image, $this->folderName);
                $uploadedPath = $this->fileService->uploadFile($request->file('image'), $this->folderName);
                $portfolio->image = $uploadedPath;
            }

            $portfolio->save();

            return response([
                'message' => 'Portfolio Updated Successfully',
                'data' => $portfolio,
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            return response([
                'status' => false,
                'error' => $th->getMessage(), // Include the error message
                'trace' => config('app.debug') ? $th->getTrace() : null, // Include trace only in debug mode
            ], 500);
        }
    }
    

    public function deletePortfolio(Request $request)
    {
        try {
            //code...
            $id = $request->id;
            $image = $request->image;
            Portfolio::where('id', $id)->delete();

            $this->fileService->deleteFile($image, $this->folderName);

            return response([
                'message' => 'Portfolio Deleted Successfully'
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
