<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TestController extends Controller
{

    public function save(Request $request)
    {
        $str = $request['base64_image'];
        // Se selecciona la parte de la imagen del string recibido
        $base64 = substr($str, strpos($str, ",")+1);

        //decode base64 string
        $decoded = base64_decode($base64);

        $destinationPath = storage_path('app/public/images');

        if (!is_dir($destinationPath))
        {
            mkdir($destinationPath, 0777, true);
        }

        $filename = md5($decoded).'.png';

        file_put_contents($destinationPath . '/' . $filename, $decoded);

        $path = Storage::url('40e93034a4ad6ee6b7eab1fe389195ad.png');


        // $image = file_put_contents('img.png', $decoded);
        // //create png from decoded base 64 string and save the image in the images folder
        // $path = $image->store('images');

        //send result - the url of the png or 0
        return response()->json([
                'epa' => 'joe',
                'path' => $path
        ]);
    }
}
