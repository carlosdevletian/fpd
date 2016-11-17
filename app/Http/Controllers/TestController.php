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
        $imagick = new Imagick(storage_path('app/public/images/ojala.png'));
        $convert = base64_decode("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");
        $replacement = new Imagick();
        $replacement->readImageBlob($convert);
        // header('Content-type: image/png');
        // echo $replacement->getImageBlob();
        // dd('epa');
        $imagick->remapImage($replacement, 0);
        $imagick->writeImage(storage_path('app/public/images/remap.png'));

        

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

        return view('colors', compact('array', 'common_colors'));
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
