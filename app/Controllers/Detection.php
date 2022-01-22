<?php

namespace App\Controllers;
use App\Models\DetectionModel;

use Config\Paths;

class Detection extends BaseController
{
    protected  $modell;
    public function __construct()
    {
        $this->modell = new DetectionModel();
    }

    public function index()
    {
        $result = $this->modell->getProcessedPictures();
        $data = ['pics' => $result];

        echo view('templates/header');
        echo view('pages/detect',$data);
        echo view('templates/footer');
    }

    public function getpicture($id){
        helper('path');

        $pic_path =$this->modell->getPicturePath($id);

        $filename = WRITEPATH.'uploads'.DIRECTORY_SEPARATOR.$pic_path;
        $handle = fopen($filename, "rb");
        $contents = fread($handle, filesize($filename));
        fclose($handle);

        $this->response->setContentType('image/jpeg');
        echo $contents;
    }

    public function getdetection($id){
        $result = $this->modell->getDetection($id);
        return json_encode($result);
    }
}
