<?php


namespace app\models;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;
use yii\helpers\Json;
use yii\helpers\FileHelper;

class ImageUploader extends Model {
    const USER_PHOTO_DIR='uploads/userphotos/';
    const ITEM_CONTENT_DIR='uploads/content/';
    const PATH_PREFIX='@web/';
    
    public $user_photo;
    public $content_images;
    
    public function rules() {
        return [
            [['content_images'],'file', 'extensions'=>['jpg','png'], 'checkExtensionByMimeType'=>false, 'maxFiles'=>10],
            ['user_photo', 'file', 'extensions'=>'jpg, png', 'checkExtensionByMimeType'=>false],   
        ];
    }
    
    public function pathToUserPhoto()
    {
        return self::PATH_PREFIX.self::USER_PHOTO_DIR;
    }
    
    public function pathToContent()
    {
        return self::PATH_PREFIX.self::ITEM_CONTENT_DIR;
    }
    
    public function uploadUserPhoto()
    {
        if($this->user_photo && $this->validate())
        {
            return $this->setImageNameAndSave(self::USER_PHOTO_DIR, $this->user_photo);
        }
        return false;
    }
    
    public function uploadContentPhotos()
    {
        if($this->content_images && $this->validate())
        {
            foreach($this->content_images as $image)
            {
                $bulk[]=$this->setImageNameAndSave(self::ITEM_CONTENT_DIR, $image);
            }
            return Json::encode($bulk);
        }
        return false;
    }
    
    private function setImageNameAndSave($folder,$image)
    {
        do{
            $name=Yii::$app->getSecurity()->generateRandomString(12);
            $fullPath=$folder.$name.".".$image->extension;
        }
        while (file_exists($fullPath));
        if($image->saveAs($fullPath))
        {
            return $name.".".$image->extension;
        }
    }
}
