<?php

namespace App\Http\Controllers;

use Imagick;
use Illuminate\Http\Request;
use League\ColorExtractor\Color;
use League\ColorExtractor\Palette;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use League\ColorExtractor\ColorExtractor;

class TestController extends Controller
{
    /**
     * Creates the image from the request, filters it down to the desirable quantity of colors and stores it in the storage folder.
     *
     * @return void
     */
    public function save(Request $request)
    {
        $directory = $this->createDirectories();
        $image = $this->createImageFromBase64($request['base64_image']);
        $destinationPath = $this->storeImage($directory, $image);
        $this->filterImage(8, $destinationPath);

        return response()->json([
            'message' => 'Image successfully generated'
        ]);
    }

    /**
     * Searches through the images directory and specifies the quantity of colors
     * for each image in that folder.
     *
     * @return void
     */
    public function colors()
    {
        //Seleccionar todas las imagenes en storage
        $images = File::allFiles(storage_path('app/public/images'));

        //Se crea array de imagenes que se enviaran al view
        $array = [];

        //Se recorren las imagenes de storage
        foreach ($images as $image) {
            //Se obtiene el nombre de la imagen
            $image = (string) $image;

            //Se genera el palette de la imagen
            $palette_all = Palette::fromFilename($image);

            $extractor = new ColorExtractor($palette_all);

            //Extrae los 6 colores mas representativos
            $palette = $extractor->extract(6);

            //Se genera un objeto Imagick a partir de la imagen
            $imagick = new Imagick($image);

            //Se genera un objeto Imagick vacio el cual contendrá una imagen a partir de cada color del palette generado
            $palette_color = new Imagick();

            //Se crea un array que contendrá los colores del palette
            $colors = [];

            //Se recorre cada color del palette
            foreach($palette as $color) {
                $colors[] = [
                    'color' => Color::fromIntToHex($color)
                ];

                //Se genera una nueva imagen a partir del color del palette
                $palette_color->newImage(10, 10, Color::fromIntToHex($color));
            }
            $palette_color->resetIterator();

            //Se juntan las imagenes en una sola imagen
            $palette_image = $palette_color->appendImages(true);
            $palette_image->setImageFormat("png");

            //Se hace el remap utilizando la imagen que contiene el palette
            $imagick->remapImage($palette_image, 1);

            //Se guarda la nueva imagen en storage
            $path = storage_path('app/public/images/'. uniqid() . '.png');
            $imagick->writeImage($path);

            //Se guarda el array con la imagen nueva y los colores
            $array[] = [
                'colors' => $colors,
                'path' => File::name($path)
            ];
        }

        return view('colors', compact('array'));
    }

    /**
     * Uses Imagick to reduce the given image to the specified quantity of colors.
     *
     * @return void
     */
    private function filterImage($colorQuantity, $destinationPath)
    {
        $imagick = new \Imagick( $destinationPath );
        $imagick->quantizeImage($colorQuantity, Imagick::COLORSPACE_RGB, 0, true, false);
        var_dump('sí quantizó');
        // $imagick->posterizeImage(2, false);
        $imagick->setImageFormat('png');
        $image_name = 'F' . substr($destinationPath, 54);
        $image_path = substr($destinationPath, 0, -38);
        $imagick->writeImage($image_path . $image_name);
    }

     /**
     * Create the directories for the files.
     *
     * @return void
     */
    protected function createDirectories()
    {
        $path = storage_path('app/public/images');
        if (! is_dir($path) ) {
            return mkdir($path, 0777, true);
        }
        return $path;
    }

    /**
     * Gets the encoded base64 string from the request and decodes it.
     *
     * @return created image
     */
    protected function createImageFromBase64($base64String)
    {
         // Se selecciona la parte de la imagen del string recibido
        $encoded = substr($base64String, strpos($base64String, ",")+1);

        return base64_decode($encoded);
    }

    /**
     * Stores the given image in the specified directory.
     *
     * @return string $filepath
     */
    protected function storeImage($directory, $image)
    {
        $filename = 'O-' . md5($image) . '.png';
        $filepath = $directory . '/' . $filename;

        file_put_contents($filepath, $image);

        return $filepath;
    }
}
