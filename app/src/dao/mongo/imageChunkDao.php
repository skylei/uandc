<?php
/**
 * Created by PhpStorm.
 * User: crab
 * Date: 2015/2/27
 * Time: 22:48
 */
namespace src\dao\mongo;
class imageChunkDao extends \components\BaseMongoDao
{
    public $collection = 'crab_img.chunks';


    public function save($data){
        return $this->getGridFS()->storeFile($data['file'], $data['info']);
    }

    public function getOne($query){
        return $this->Ndb->findOne($query);

    }

}