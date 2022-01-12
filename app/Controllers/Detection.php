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

    public function getpicture($id){
        $db = db_connect('default',false);
        $builder = $db->table('pictures');
        return $id;
        $builder->where('id' ,$id);

        header('Content-Type: image/jpeg');
        readfile($img);
    }
}
