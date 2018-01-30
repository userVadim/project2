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
    
    public $username;
    public $user_photo;
    public $content_images;
    
    public function rules() {
        return [
            [['content_images'],'file', 'extensions'=>['jpg','png'], 'checkExtensionByMimeType'=>false, 'maxFiles'=>10],
            ['user_photo', 'file', 'extensions'=>'jpg, png', 'checkExtensionByMimeType'=>false],
            
        ];
    }
    
    public function uploadUserPhoto()
    {
        if($this->user_photo && $this->validate())
        {
            $this->user_photo->saveAs(self::USER_PHOTO_DIR.$this->username.time().".".$this->user_photo->extension);
            return $this->username.time().".".$this->user_photo->extension;
        }
        return false;
    }
    
    public function uploadContentPhotos()
    {
        if($this->content_images && $this->validate())
        {
            foreach($this->content_images as $image)
            {
                do{
                    $name=Yii::$app->getSecurity()->generateRandomString(12);
                    $fullPath=self::ITEM_CONTENT_DIR.$name.".".$image->extension;
                }
                while (file_exists($fullPath));
                $image->saveAs($fullPath);
                $bulk[]=$name.".".$image->extension;
            }
            return Json::encode($bulk);
        }

    }
}
