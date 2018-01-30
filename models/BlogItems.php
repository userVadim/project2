<?php


namespace app\models;
use yii\db\ActiveRecord;
use app\models\User;

class BlogItems extends ActiveRecord{
        
    public static function tableName() {
        return "blog_items";
    }
    
    public function rules()
    {
        return [
            [['content', 'title','comments'], 'required'],
            ['content', 'string'],
            ['title', 'string', 'max'=>256],
        ];
    }
    
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id'=>'author']);
    }
}
