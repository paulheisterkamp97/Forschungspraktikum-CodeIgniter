<?php

namespace App\Controllers;
use App\Models\DetectionModel;
use App\Helpers\PDFTemplate;


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

    public function getPicture($id){
        helper('path');

        $pic_path =$this->modell->getPicturePath($id);

        $filename = WRITEPATH.'uploads'.DIRECTORY_SEPARATOR.$pic_path;
        $handle = fopen($filename, "rb");
        $contents = fread($handle, filesize($filename));
        fclose($handle);

        $this->response->setContentType('image/jpeg');
        echo $contents;
    }

    public function getDetection($id){
        $result = $this->modell->getDetection($id);
        return json_encode($result);
    }

    public function getClasses(){
        $result = $this->modell->getClasses();
        return json_encode($result);
    }

    public function updateDetection(){
        $data = $this->request->getVar('detection');
        $picid = $this->request->getVar('pictureId');
        $this->modell->updateHitboxes($picid,$data);

        if($this->request->getVar('saveTrain')){




        }

    }

    public function createPDF($id){
        $this->response->setContentType('application/pdf');
        $template = new PDFTemplate();
        $partlist = $this->modell->getPartList($id);
        $pdf = $template->createPdf($partlist);
        $pdf->Output('recite.pdf', 'I');
    }
}
