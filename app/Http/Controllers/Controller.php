<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Symfony\Component\HttpFoundation\File\File;
use Illuminate\Http\Request;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function saveImageFromUrl($url, $path)
    {
        $extension = pathinfo($url,PATHINFO_EXTENSION);
        $fullName =  $path . '/' . md5(microtime()) . '.' . $extension;
        try{
            $image = Image::make($url)->encode('jpg', 60)->resize(800,1200);
            $image->save(public_path($fullName));
            $saved_image_uri = $image->dirname.'/'.$image->basename;
            $uploaded_thumbnail_image = Storage::putFileAs('public/', new File($saved_image_uri), $fullName);
            $image->destroy();
            unlink($saved_image_uri);
        }
        catch(\Exception $e){
            $fullName = 'No image';
        }

        return $fullName;
    }
}

