<?php
namespace backend\modules\user\controllers;

use Yii;
use yii\web\Controller;
use yii\helpers\Url;

use ant\base\MultiModel;
use ant\address\models\Address;
use ant\user\models\UserProfile;
use ant\user\models\UserProfileSearch;
use frontend\filters\AccessControl;
use frontend\filters\AccessRule;

/**
 * Profiles controller for the `user` module
 */
class ProfilesController extends Controller
{
	/**
     * layout overide
     * @var
     */
    public $layout = '//left-sidenav';

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
     * @inheritdoc
     */
    public function behaviors(){
    	return
        [
			'access' => [
				'class' => \ant\rbac\ModelAccessControl::className(),
			],
        ];
    }

    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new UserProfileSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

    	return $this->render('profiles', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'sideNav' => $this->getSideNav(),
        ]);
    }
	
	/*public function actionView($id) {
		$model = UserProfile::findOne($id);
		$this->checkAccess('view', $model, ['attribute' => 'user_id']);
	}*/

    public function actionCreate()
    {
        $profile = new UserProfile();
        $profile->scenario = UserProfile::SCENARIO_SENSITIVE;
        $address = $profile->address;
        $address->scenario = Address::SCENARIO_NO_REQUIRED;
        $model = new MultiModel([
            'models' => [
                'profile' => $profile,
                'address' => $address,
            ]
        ]);

        if($model->load(Yii::$app->request->post()) && $model->validate()){

            if($model->save()){
                Yii::$app->user->identity->link('profiles', $profile);

                Yii::$app->session->setFlash('success', 'Profile created.');

                return Yii::$app->response->redirect(Url::to(['/user/profiles/update', 'id' => $model->getModel('profile')->id]));
            } else {
                Yii::$app->session->setFlash('error', 'Save profile eror, please try again.');
            }
        }

        return $this->render('create', [
            'sideNav' => $this->getSideNav(),
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
    {
        $profile = $this->findModel($id);
        $profile->scenario = UserProfile::SCENARIO_SENSITIVE;
        $address = $profile->address;
        $address->scenario = Address::SCENARIO_NO_REQUIRED;
        $model = new MultiModel([
            'models' => [
                'profile' => $profile,
                'address' => $address,
            ]
        ]);

        if($model->load(Yii::$app->request->post()) && $model->validate()){

            if($model->save()){

                Yii::$app->session->setFlash('success', 'Profile updated.');

                return Yii::$app->response->redirect(Url::current());
            } else {
                Yii::$app->session->setFlash('error', 'Save profile eror, please try again.');
            }
        }

        return $this->render('update', [
            'sideNav' => $this->getSideNav(),
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Event model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();

        return $this->redirect(Yii::$app->request->referrer);
    }

    private function findModel($id)
    {
        if (($model = UserProfile::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
