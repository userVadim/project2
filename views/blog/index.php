<?php
use yii\helpers\StringHelper;
use yii\helpers\Html;
use app\models\ImageUploader;
?>

<div class="home-div">
    <?php
    foreach($items as $item)
    {
        ?>
    <div class="one-item-home">
        <h3><?= Html::a($item->title, ['blog/view-item','id'=>$item->id])?></h3>
        <p><?= StringHelper::truncate($item->content, 250)?></p>
        <p><?=Html::img(ImageUploader::pathToUserPhoto().$item->user->photo,['height'=>'30px'])?><?=$item->user->username?></p>
    </div>
        <?php
    }
    ?>
</div>



