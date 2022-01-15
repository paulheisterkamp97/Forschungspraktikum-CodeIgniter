<?php

namespace App\Controllers;


use Config\Paths;

class Detection extends BaseController
{
    public function index()
    {
        $db = db_connect();
        $builder = $db->table('pictures');
        $result = $builder->get()->getResult();
        $data = ['pics' => $result];
        echo view('templates/header');
        echo view('pages/detect',$data);
        echo view('templates/footer');
    }

    public function getpicture($id){
        helper('path');
        $db = db_connect();
        $builder = $db->table('pictures');
        $builder->select('path');
        $query = $builder->getWhere(['id'=> $id])->getFirstRow();
        $pic_path = $query->path;

        $filename = WRITEPATH.'uploads'.DIRECTORY_SEPARATOR.$pic_path;
        $handle = fopen($filename, "rb");
        $contents = fread($handle, filesize($filename));
        fclose($handle);

        $this->response->setContentType('image/jpeg');
        echo $contents;
    }

    public function getdetection($id){
        $db = db_connect();
        $builder = $db->table('hitboxes');
        $builder->select('*');
        $builder->join('pictures', 'pictures.id = hitboxes.id');
        //$builder->getWhere(['id'=> $id]);
        $result = $builder->getWhere(['pictures.id'=> $id])->getResult();
        return json_encode($result);

    }
}
