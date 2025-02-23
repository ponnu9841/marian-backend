<?php

namespace App\Services;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class FileService
{
    /**
     * Delete an image from the server.
     *
     * @param string $imageUrl
     * @return void
     */
    public function deleteFile($fileUrl, $folderName)
    {
        // Parse the URL
        $path = parse_url($fileUrl, PHP_URL_PATH);
        // Split the path by '/'
        $parts = explode('/', $path);
        // Extract the year from the parts
        $year = $parts[3];
        // Extract the filename from the parts
        $filename = end($parts);
        // Delete the file
        File::delete(public_path() . '/uploads/' . $folderName . '/' . $year . '/' . $filename);
    }

    public function uploadFile($image, $folderName)
    {
        $ext = $image->getClientOriginalExtension();
        $imageName = Str::random(20) . '_' . time() . '.' . $ext;
        $uploadFolder = '/uploads/' . $folderName . '/' . date("Y");

        $destPath = public_path() . $uploadFolder;

        $image->move($destPath, $imageName);
        $baseUrl = env('APP_URL');
        return $baseUrl . $uploadFolder. '/' . $imageName;
    }
}
