<?php
namespace ant\user\backend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\helpers\Json;

use ant\base\MultiModel;

use Intervention\Image\ImageManagerStatic;
use trntv\filekit\actions\DeleteAction;
use trntv\filekit\actions\UploadAction;

use ant\address\models\Address;
use ant\address\models\AddressCountry;
use ant\address\models\AddressZone;

use ant\user\models\PasswordForm;
use ant\user\models\EmailChangeForm;
use ant\user\models\EmailChangeRequestForm;

/**
 * Default controller for the `user` module
 */
class SettingController extends Controller
{
    /**
     * layout overide
     * @var
     */
    //public $layout = '//left-sidenav';

    /**
     * SideNav
     * @return array
     */
    private function getSideNav(){
        return [
            'heading' => 'Settings',
            'items' => [
                [
                    'url' => ['/user/setting/index'],
                    'label' => 'Update Profile',
                ],
                [
                    'url' => ['/user/setting/email'],
                    'label' => 'Change E-mail',
                ],
                [
                    'url' => ['/user/setting/password'],
                    'label' => 'Change Password',
                ],
                [
                    'url' => ['/user/profiles/index'],
                    'label' => 'My Profiles',
                ],
            ]
        ];
    }

    /**
     * @return array
     */
    public function actions()
    {
        return
        [
            'avatar-upload' =>
            [
                'class' => UploadAction::className(),
                'deleteRoute' => 'avatar-delete',
                'on afterSave' => function ($event) {
                    /* @var $file \League\Flysystem\File */
                    $file = $event->file;
                    $img = ImageManagerStatic::make($file->read())->fit(215, 215);
                    $file->put($img->encode());
                }
            ],
            'avatar-delete' => [
                'class' => DeleteAction::className()
            ]
        ];
    }

	/**
     * @inheritdoc
     */
    public function behaviors()
    {
    	return
        [
            'verbs' =>
            [
               'class' => VerbFilter::className(),
               'actions' =>
               [
                   'zone-list' => ['post'],
               ],
           ],
        ];
    }

    public function actionIndex()
    {
        $profile = Yii::$app->user->identity->profile;
        $address = $profile->address;
        $address->scenario = Address::SCENARIO_NO_REQUIRED;
        $model = new MultiModel([
            'models' => [
                'profile' => $profile,
                'address' => $address,
            ]
        ]);

        if ($model->load(Yii::$app->request->post())) {
            if($model->save()){
                Yii::$app->session->setFlash('success', 'Profile is updated successfully. ');
                return $this->redirect(Url::current());
            } else {
                Yii::$app->session->setFlash('danger', 'Failed to update profile. '.(YII_DEBUG ? print_r($model->errors, 1) : ''    ));
            }
        }

        return $this->render('setting', [
            'sideNav' => $this->getSideNav(),
            'model' => $model,
        ]);
    }

    public function actionPassword()
    {
        $model = new PasswordForm();
        $model->setUser(Yii::$app->user->identity);
        if ($model->load(Yii::$app->request->post())) {
            if ($model->changePassword()) {
                Yii::$app->session->setFlash('success', 'Change password success');
                return Yii::$app->response->redirect(Url::current());
            } else {
                Yii::$app->session->setFlash('danger', 'Invalid old password');
            }
        }

        return $this->render('password', [
            'sideNav' => $this->getSideNav(),
            'model' => $model,
        ]);
    }

    public function actionProfile()
    {

        $profile = Yii::$app->user->identity->profile;
        $address = $profile->address;
        $address->scenario = Address::SCENARIO_NO_REQUIRED;
        $model = new MultiModel([
            'models' => [
                'profile' => $profile,
                'address' => $address,
            ]
        ]);

        if ($model->load(Yii::$app->request->post())) {
            if($model->save()){
                Yii::$app->session->setFlash('success', 'Update Profile success');
                return Yii::$app->response->redirect(Url::current());

            } else {
                Yii::$app->session->setFlash('danger', 'Update Profile fail');
            }
        }

        return $this->render('profile', [
            'sideNav' => $this->getSideNav(),
            'model' => $model,
        ]);
    }

    public function actionTokenChangeEmail($tokenkey, $email)
    {
        $model = new EmailChangeForm();
        if($user = $model->changeEmailByToken($tokenkey, $email)) {
            Yii::$app->user->login($user);

            return $this->render('emailChangeSuccess', [
                'sideNav' => $this->getSideNav()
            ]);
        }
    }

    public function actionEmail()
    {
        $model = new EmailChangeRequestForm();

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return Yii::$app->response->redirect(Url::current());
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for email provided.');
            }
        }

        if(Yii::$app->request->isAjax){
            return $this->renderAjax('email', [
                'sideNav' => $this->getSideNav(),
                'model' => $model,
            ]);
        } else {
            return $this->render('email', [
                'sideNav' => $this->getSideNav(),
                'model' => $model,
            ]);
        }
    }

    public function actionZoneList()
    {
        $depDrop = [];
        if (isset($_POST['depdrop_parents'])) {
            $parents = $_POST['depdrop_parents'];

            if ($parents != null) {
                $id = $parents[0];

                $selected = '';
                if (!empty($_POST['depdrop_params'])) {
                    $params = $_POST['depdrop_params'];
                    $selected = $params[0];
                }

                $depDrop =  AddressZone::find()->where(['country_id'=>$id])->dropDownListForDepDrop();
                echo Json::encode(['output'=>$depDrop, 'selected'=> $selected]);
                return;
            }
        }
        echo Json::encode(['output'=>'', 'selected'=>'']);
    }

}
