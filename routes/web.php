<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'HomeController@home');

Route::get('/image/{filename}', function($filename) {
    $path = storage_path() . '/app/public/images/' . $filename . '.png';

    $file = File::get($path);
    $type = File::mimeType($path);

    $response = Response::make($file, 200);
    $response->header("Content-Type", $type);
    return $response;
})->name('image');

Route::get('/filter', function() {
    $imagePath = public_path('images/pepsi.png');
    $imagick = new \Imagick($imagePath);
    $matrix = [
        [0, 0, 0],
        [0,  5,  0],
        [-1, 0, -1],
    ];
     
    $kernel = \ImagickKernel::fromMatrix($matrix);
    $strength = 0.5;
    $kernel->scale($strength, \Imagick::NORMALIZE_KERNEL_VALUE);
    $kernel->addUnityKernel(1 - $strength);
 
    $imagick->filter($kernel);
    header("Content-Type: image/jpg");
    echo $imagick->getImageBlob();
});

Route::post('/save', 'TestController@save');

Route::get('/colors', 'TestController@colors');
