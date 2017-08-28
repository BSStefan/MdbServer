<?php

namespace App\Http\Controllers\Web;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;

class ImageController extends Controller
{
    public function getImage(Request $request)
    {
        $file = Storage::disk('local')->get('public/'.$request->name);
        return new Response($file, 200);
    }
}
