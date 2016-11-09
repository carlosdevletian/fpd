<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

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

    public function colors()
    {
        //https://github.com/thephpleague/color-extractor  Esta es la pagina del paquete

        //Imagenes de prueba en carpeta images van de la a a la d
        $palette = Palette::fromFilename('images/b.png');

        // Si habilitas este foreach te muestra los colores por nombre que tiene la imagen
        foreach($palette as $color => $count) {
            // colors are represented by integers and converted to hex
            // echo Color::fromIntToHex($color), ': ', $count, "\n";
        }

        // Devuelve los colores mas usados
        $topFive = $palette->getMostUsedColors(5);

        //Creo una imagen nueva en base a la anterior
        $im = imagecreatefrompng('images/b.png');

        //Limito la imagen a 10 colores
        imagetruecolortopalette($im, false, 10);

        // Se guarda la imagen (se ve terrible)
        imagepng($im, 'newimage.png');

        // imprimo la cantidad de colores y el top five de colores (Estan en int, hay que pasarlos a hex)
        dd('Number of colors: '. count($palette), $topFive);
    }
}
