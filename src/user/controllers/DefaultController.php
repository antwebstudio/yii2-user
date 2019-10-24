<?php
namespace ant\user\controllers;

use Yii;
use yii\web\Controller;

use ant\base\MultiModel;
use frontend\filters\AccessControl;
use frontend\filters\AccessRule;

use ant\user\models\UserForm;

/**
 * Default controller for the `user` module
 */
class DefaultController extends Controller
{
	/**
     * @inheritdoc
     */
    public function behavoirs(){
    	return 
        [
            'access' => 
            [
                'class' => AccessControl::className(),
                'ruleConfig' => ['class' => AccessRule::className()],
                'rules' => 
                [
                    [
                        'actions' => ['profile'],
                        'allow' => true,
                        'roles' => [AccessRule::ROLE_ACTIVATED],
                    ],
                    
                ],
            ],
        ];
    }

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
    	
    }

    /**
	 * User Profile.
	 * 
     * @return mixed
     */
    public function actionProfile(){
        $userForm = new UserForm();
        $userForm->setUser(Yii::$app->user->identity);

        $model = new MultiModel([
            'models' => [
                'user' => $userForm,
                'profile' => Yii::$app->user->identity->userProfile
            ]
        ]);

        /*
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $locale = $model->getModel('profile')->locale;
            Yii::$app->session->setFlash('forceUpdateLocale');
            Yii::$app->session->setFlash('alert', [
                'options' => ['class'=>'alert-success'],
                'body' => Yii::t('frontend', 'Your account has been successfully saved', [], $locale)
            ]);
            return $this->refresh();
        }*/
        return $this->render('profile', ['model'=>$model]);
    }
}