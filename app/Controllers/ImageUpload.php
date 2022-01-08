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

            $img->store('in', $this->request->getVar('filename').'.jpg');

            echo view('templates/header');
            echo view('pages/upload_sucess');
            echo view('templates/footer');
        } else {

            return redirect()->to('upload');
        }
    }

    public function list_uploaded()
    {
        $filenames = scandir('../writable/uploads/in');
        $pictures = [];
        foreach ($filenames as $fn) {
            if( str_ends_with($fn,'.jpg') ) {
                array_push($pictures, $fn);
            }
        }
        foreach ($pictures as $fn) {
            echo '<p>'.$fn.'</p>';
        }
    }


}