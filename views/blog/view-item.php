<?php
use yii\widgets\Menu;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\ActiveField;
use yii\helpers\Json;
use app\models\ImageUploader;
?>

<div class="view-item">
    <div class="menu">
        <?php 
            if($author)
            {
                $publishBtn=['label'=>'Publish Item', 'url'=>['blog/view-item','id'=>$item['id'],'act'=>'publish']];
                if($item['published']==1)
                {
                    $publishBtn=['label'=>'Hide Item', 'url'=>['blog/view-item','id'=>$item['id'],'act'=>'hide']];
                }
                echo Menu::widget(
                    [
                        'items'=>[
                            $publishBtn,
                            ['label'=>'Update Item', 'url'=>['blog/update-item','id'=>$item['id']]],
                            ['label'=>'Delete Item', 'url'=>['blog/view-item', 'id'=>$item['id'],'act'=>'delete']]
                        ]
                    ]
                );
            }
        ?>
    </div>
    
    <h1><?=$item->title?></h1>
    <div class="content-images">
        <?php
        if($item->images)
        {
            foreach(Json::decode($item->images) as $img)
            {
                echo Html::img(ImageUploader::pathToContent().$img,['height'=>'150px']);
            }
        }
        ?>
    </div>
    <div class='item-content'>
        <?= Html::encode($item->content)?>
    </div>
        
    <?php
    if($item->comments)
    {
    ?>
        <div class="comments">
            <h3>COMMENTS</h3>
            <?php
            foreach($comments as $comment)
            {
                ?>
                <div class="item-comment">
                    <p class="comment-text"><?=$comment->comment?></p>
                    <p><?=Html::img('@web/uploads/userphotos/'.$comment->user->photo,['height'=>'30px'])?><?=$comment->user->username?></p>
                    <?php
                    if($comment->user_id==Yii::$app->user->id)
                    {
                        echo Html::a("delete",['#'],['class'=>'delbtn', 'for'=>$comment->id]);
                        echo Html::a("update",['#comtext'],['class'=>'updbtn', 'for'=>$comment->id]);
                    }
                    ?>
                </div>
                <?php
            }
            
            
            if(Yii::$app->user->isGuest)
            {
            ?>
            <h5>If you want to leave a comment, please <?=Html::a('Login', $url='login')?></h5>
            <?php
            }
            else
            {
                $form= ActiveForm::begin();
                echo Html::hiddenInput('update', $value = null, $options=['class'=>'update_input']);
                echo $form->field($commModel, 'comment')->textarea(['id'=>'comtext']);
                echo Html::submitButton('send');
                $form= ActiveForm::end();
            }
            ?>
            
        </div>
    <?php
    }
    ?>
</div>

