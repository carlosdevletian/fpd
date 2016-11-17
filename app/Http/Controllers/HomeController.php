<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class HomeController extends Controller
{
    public function home()
    {
        $files = File::allFiles(public_path('images/icons'));
        $images = [];
        foreach ($files as $image) {
            $images[] = substr((string) $image, 34);
        }

        return view('welcome', compact('images'));
    }
}
