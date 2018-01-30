<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\User;
use app\models\ImageUploader;
use yii\web\UploadedFile;
use app\models\BlogItems;
use app\models\Comments;



class BlogController extends Controller {
    
    
    private function relocateActiveUsers()
    {
        if(!Yii::$app->user->isGuest)
        {
            return $this->goBack();
            return $this->goHome();
        }
    }
            
    
    public function actionLogin()
    {
        $this->relocateActiveUsers();
        
        $model=new LoginForm();
        if($model->load(Yii::$app->request->post()) && $model->login())
        {
            return $this->goBack();
        }
        return $this->render('login',compact('model'));
    }
    
    
    public function actionSignup()
    {
        $this->relocateActiveUsers();
        
        $model=new User();
        $photo=new ImageUploader();
        
        if($model->load(Yii::$app->request->post()) && $model->validate())
        {
            $password=$model->password;
            $model->password=Yii::$app->getSecurity()->generatePasswordHash($model->password);
            $photo->username=$model->username;
            $photo->user_photo=UploadedFile::getInstance($photo, 'user_photo');
            $model->photo=$photo->uploadUserPhoto();
            if($model->save())
            {
                $this->autologin($model->username, $password);
            }
        }
        
        return $this->render('signup', compact('model','photo'));
    }
    
    private function autologin($username, $password)
    {
        $model=new LoginForm();
        $model->username=$username;
        $model->password=$password;
        if($model->login())
        {
            return $this->goBack();
        }
    }
    
    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goBack();
    }
    
    public function actionIndex()
    {
        $items=BlogItems::find()->where(['published'=>1])->with('user')->orderBy(['date'=>SORT_DESC])->all();
        return $this->render('index', compact('items'));
    }
    
    public function actionMyItems()
    {
        if($this->isAuthorGlobally())
        {
            $items = BlogItems::find()->where(['author'=>Yii::$app->user->id])->orderBy(['date'=>SORT_DESC])->all();
            return $this->render('index', compact('items'));
        }
        return $this->goHome();
    }
    
    public function actionCreateItem()
    {
        if(Yii::$app->user->isGuest)
        {
            Yii::$app->session->setFlash('login to create', 'Loged Users can create items only. Please, Login or Sign in!');
            return Yii::$app->response->redirect('login');
        }
        $blogItem=new BlogItems();
        $images= new ImageUploader();

        

        if($blogItem->load(Yii::$app->request->post()) && $blogItem->validate())
        {
            $images->username=Yii::$app->user->identity->username;
            $images->content_images= UploadedFile::getInstances($images, 'content_images');
            $blogItem->images=$images->uploadContentPhotos();

            $blogItem->date=date('Y-m-d H:i:s');
            $blogItem->author=Yii::$app->user->id;
            if($blogItem->save())
            {
                return Yii::$app->response->redirect(['blog/view-item','id'=>$blogItem->id]);
            }

        }
        
        return $this->render('create-item', compact('blogItem','images'));
    }
    
    public function actionViewItem()
    {
        $item= BlogItems::find()->where(['id'=>Yii::$app->request->get('id')])->one();
        if(!$item)
        {
            return $this->goHome();
        }
        $author=$this->isItemAuthor($item->author);
        if(!$item->published && !$author)
        {
            return $this->goBack();
        }

        if($author)
        {
            $this->actionsWithItem();
        }    

        $comments=Comments::find()->where(['item_id'=>Yii::$app->request->get('id')])
                ->with('user')->orderBy('date')->all();;
       $commModel=$this->newComments();

        return $this->render('view-item', compact('item','author','comments', 'commModel'));
    }
    
    private function newComments()
    {
        $model= new Comments();
        
        if($model->load(Yii::$app->request->post()) && $model->validate())
        {
            if(Yii::$app->request->post('update'))
            {
                $comment=$model->find()->where(['id'=>Yii::$app->request->post('update')])->one();
                $comment->load(Yii::$app->request->post());
                $comment->update();
            }
            else
            {
                $model->user_id=Yii::$app->user->id;
                $model->item_id=Yii::$app->request->get('id');
                $model->date=date('Y-m-d H:i:s');
                $model->save();
            }
            $this->refresh();
        }

        return $model;
    }
    
    public function actionDeleteComment()
    {
        if(Yii::$app->request->isAjax)
        {
            $model= Comments::findOne(Yii::$app->request->post('id'));
            if($model->delete())
            {
                echo Yii::$app->request->post('id');
            } 
        }
    }
    
    public function isItemAuthor($authorId)
    {
        if($authorId==Yii::$app->user->id)
        {
            return true;
        }
        return false;
    }
    
    private function actionsWithItem()
    {
        $action=[
            'publish'=>1,
            'hide'=>0,
        ];
        
        if(Yii::$app->request->get('act') && Yii::$app->request->get('id'))
        {
            $item= BlogItems::findOne(Yii::$app->request->get('id'));
            if(array_key_exists(Yii::$app->request->get('act'),$action))
            {
                $item->published=$action[Yii::$app->request->get('act')];
                $item->update();
            }
            elseif(Yii::$app->request->get('act')=='delete')
            {
                $item->delete();
                if(!$this->isAuthorGlobally())
                {
                    return $this->goHome();
                }
            }
            return $this->redirect(Yii::$app->request->referrer);
        }
    }
    
    
    public function isAuthorGlobally()
    {
        $items= BlogItems::find()->where(['author'=>Yii::$app->user->id])->count();
        if($items>0)
        {
            return true;
        }
        return false;
    }


    public function actionUpdateItem()
    {
        $item= BlogItems::findOne(Yii::$app->request->get('id'));
        if($this->isItemAuthor($item->author)) 
        {
            if($item->load(Yii::$app->request->post()) && $item->validate())
            {

                $item->update();
                return Yii::$app->response->redirect(['blog/view-item','id'=>$item->id]);
            }
            return $this->render('update-item', compact('item'));
        }
        return $this->goHome();     
    }
    
    
    
    
    
    
    
    
}
