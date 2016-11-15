<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Imagick;

//Paquete Colores
use League\ColorExtractor\Color;
use League\ColorExtractor\ColorExtractor;
use League\ColorExtractor\Palette;

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

        // $path = Storage::url('prueba.png');

        // quantizeImage($imagePath, $numberColors, $colorSpace, $treeDepth, $dither) 
        $imagick = new \Imagick( $destinationPath . '/' . $filename);
        $imagick->quantizeImage(8, Imagick::COLORSPACE_RGB, 0, false, false);
        $imagick->setImageFormat('png');
        $imagick->writeImage($destinationPath . '/prueba2.png');
        // header("Content-Type: image/png");
        // echo $imagick;


        


        // $image = file_put_contents('img.png', $decoded);
        // //create png from decoded base 64 string and save the image in the images folder
        // $path = $image->store('images');

        //send result - the url of the png or 0
        return response()->json([
            'epa' => 'joe'
        ]);
    }

    public function colors()
    {
        $path = storage_path() . '/app/public/images/prueba2.png';
        //https://github.com/thephpleague/color-extractor  Esta es la pagina del paquete

        $palette = Palette::fromFilename($path);

        // Si habilitas este foreach te muestra los colores por nombre que tiene la imagen
        $colors = [];
        $total = 0;
        foreach($palette as $color => $count) {
            // colors are represented by integers and converted to hex
            $colors[] = [ 
                'color' => Color::fromIntToHex($color),
                'quantity' => $count
            ];
            $total += $count;
        }
     
        return view('colors', compact('palette', 'colors', 'total'));
        // dd('Number of colors: '. count($palette), $topFive);
    }
}
