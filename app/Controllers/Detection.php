<?php

namespace App\Controllers;


use Config\Paths;

class Detection extends BaseController
{
    public function index()
    {
        echo view('templates/header');
        echo view('pages/detect');
        echo view('templates/footer');
    }

    public function getpicture($id){
        helper('path');
        $db = db_connect();
        $builder = $db->table('pictures');
        $builder->select('path');
        $query = $builder->getWhere(['id'=> $id]);
        print_r($query);


        //header('Content-Type: image/jpeg');
        //return WRITEPATH.'uploads'.DIRECTORY_SEPARATOR.print_r($query);
    }
}
