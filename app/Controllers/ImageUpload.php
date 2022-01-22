<?php
namespace App\Controllers;
use CodeIgniter\Files\File;
use App\Models\DetectionModel;
class ImageUpload extends BaseController {

    protected  $modell;
    public function __construct()
    {
        $this->modell = new DetectionModel();
    }

    public function index() {
        echo view('templates/header');
        echo view('pages/upload');
        echo view('templates/footer');

    }

    public function store() {
        helper(['form']);
        $validationRule = [
            'filename' =>[
                'rules' =>'required' ,
                'errors' =>[
                    'required' =>'Filename is required'
                ]
            ],
            'image' => [
                'label' => 'Image File',
                'rules' => 'uploaded[image]'
                    . '|is_image[image]'
                    . '|mime_in[image,image/jpg,image/jpeg]'
                    . '|max_size[image,10000]'
                    . '|max_dims[image,5000,5000]',
            ],
        ];
        if (! $this->validate($validationRule)) {
            $data = ['errors' => $this->validator];
            echo view('templates/header');
            echo view('pages/upload', $data);
            echo view('templates/footer');
            return ;
        }

        $img = $this->request->getFile('image');

        if (! $img->hasMoved()) {

            $path = $img->store('in', $this->request->getVar('filename').'.jpg');
            $path = str_replace('/',DIRECTORY_SEPARATOR,$path);

            $current_dir = getcwd();

            // Database operation ----------------------------------------------

            $pic_id = $this->modell->addPicture($this->request->getVar('filename'),$path);

            // Python trigger --------------------------------------------------
            chdir('..'.DIRECTORY_SEPARATOR.'app'.DIRECTORY_SEPARATOR.'Helpers'.DIRECTORY_SEPARATOR.'python'.DIRECTORY_SEPARATOR.'script');
            if (PHP_OS == "Linux"){
                exec('venv/bin/python3 detect.py '.$pic_id);
            }else{
                exec('venv\Scripts\python detect.py '.$pic_id.' 2>&1');
            }
            chdir($current_dir);


            echo view('templates/header');
            echo view('pages/upload_sucess');
            echo view('templates/footer');
        } else {

            return redirect()->to('upload');
        }
    }
}