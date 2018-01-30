<?php

namespace app\models;
use yii\db\ActiveRecord;
use app\models\User;

class Comments extends ActiveRecord {
    
    public function rules() {
        return [
            ['comment', 'required'],
            ['comment', 'string', 'max'=>1000],
        ];
    }
    
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id'=>'user_id']);
    }
}
