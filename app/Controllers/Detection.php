<?php

namespace App\Controllers;

class Detection extends BaseController
{
    public function index()
    {
        $filenames = scandir('../writable/uploads/in');
        $pictures = [];
        foreach ($filenames as $fn) {
            if( str_ends_with($fn,'.jpg') ) {
                array_push($pictures, $fn);
            }
        }

        echo view('templates/header');
        echo view('pages/detect',$pictures);
        echo view('templates/footer');
    }
}
