<?php

namespace App\Controllers;

class Detection extends BaseController
{
    public function index()
    {
        echo view('templates/header');
        echo view('pages/detect');
        echo view('templates/footer');
    }
}
