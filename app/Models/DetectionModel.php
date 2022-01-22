<?php
namespace App\Models;
use CodeIgniter\Database\ConnectionInterface;

class DetectionModel
{
    protected  $db;
    public function __construct()
    {
        $this->db = db_connect();
    }

    public function addPicture($name,$path){
        $builder = $this->db->table('pictures');

        $db_data = [
            'name'=>$name,
            'path'=>$path,
        ];
        $builder->insert($db_data);
        return $this->db->insertID();
    }

    public function getProcessedPictures(){
        $builder = $this->db->table('pictures');
        $result = $builder->get()->getResult();
        return $result;
    }

    public function getPicturePath($id){
        $builder = $this->db->table('pictures');
        $builder->select('path');
        $query = $builder->getWhere(['id'=> $id])->getFirstRow();
        $pic_path = $query->path;
        return $pic_path;
    }

    public function getDetection($id){
        $builder = $this->db->table('hitboxes');
        $builder->select('*');
        $builder->join('pictures', 'pictures.id = hitboxes.id');
        $result = $builder->getWhere(['pictures.id'=> $id])->getResult();
        return $result;
    }

    public function deletePicture($id){
        $builder = $this->db->table('pictures');
        $builder->where('id',$id);
        $builder->delete();
    }

    public function getClasses(){
        $builder = $this->db->table('classes');
        $builder->select('*');
        return $builder->get()->getResult();
    }

}