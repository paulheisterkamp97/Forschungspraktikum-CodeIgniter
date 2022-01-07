<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index($page = 'upload')
    {

        echo view('templates/header');
        echo view('pages/'.$page);
        echo view('templates/footer');
    }
}
