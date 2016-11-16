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
        
        $images = File::allFiles(storage_path('app/public/images'));
        $array = [];
        foreach ($images as $image) {
            $image = (string) $image;
            $palette = Palette::fromFilename($image);
            $colors = [];
            $total = 0;
            foreach($palette as $color => $count) {
                $colors[] = [ 
                    'color' => Color::fromIntToHex($color),
                    'quantity' => $count
                ];
                $total += $count;
            }
            $array[] = [
                'palette' => $palette,
                'colors' => $colors,
                'total' => $total,
                'path' => File::name($image)
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
        // $imagick->posterizeImage(2, false);
        $imagick->setImageFormat('png');
        $imagick->writeImage($destinationPath);
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
        $filename = md5($image) . '.png';
        $filepath = $directory . '/' . $filename;

        file_put_contents($filepath, $image);

        return $filepath;
    }
}
