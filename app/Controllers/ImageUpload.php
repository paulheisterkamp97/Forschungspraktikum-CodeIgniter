<?php
namespace App\Controllers;
use CodeIgniter\Files\File;
class ImageUpload extends BaseController {

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

            $current_dir = getcwd();

            // Database operation ----------------------------------------------

            $db = db_connect('default',false);
            $builder = $db->table('pictures');

            $db_data = [
                'name'=>$this->request->getVar('filename'),
                'path'=>$path,
            ];
            $builder->insert($db_data);
            $pic_id = $db->insertID();
            $db->close();

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

    public function list_uploaded()
    {
        $filenames = scandir('../writable/uploads/out');
        $pictures = [];
        foreach ($filenames as $fn) {
            if( str_ends_with($fn,'.json') ) {
                array_push($pictures, $fn);
            }
        }
        return json_encode($pictures);
    }
    public function get_image($filename = null){
        //header('Content-Type: image/jpeg');
        return HOMEPATH;
        readfile(HOMEPATH,);
    }
    public function get_detection(){

    }


}