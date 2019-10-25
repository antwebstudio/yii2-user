<?php 
namespace ant\user\backend\controllers;

use Yii;
use yii\web\Controller;
use yii\helpers\Html;
use yii\filters\VerbFilter;
use yii\data\ActiveDataProvider;

use Intervention\Image\ImageManagerStatic;
use trntv\filekit\actions\DeleteAction;
use trntv\filekit\actions\UploadAction;

use ant\base\MultiModel;
use frontend\filters\AccessControl;
use frontend\filters\AccessRule;
use ant\address\models\Address;
use ant\user\models\InviteRequest;
use ant\user\models\UserInvite;
use ant\user\models\User;
use ant\user\models\UserConfigForm;
use ant\user\models\UserRoleForm;
use ant\user\models\UserConfig;
use ant\user\models\UserProfile;
use ant\user\models\UserIdentity;
use ant\user\models\UserSearch;
use ant\user\models\SignupForm;

class UserController extends Controller
{
	public $showApproveAction = true;
	
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
	
    public function actionIndex()
    {
        $searchModel = new UserSearch();
		$dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider, 
            'model' => $searchModel
        ]);
    }

    public function actionCreate() {
        $model = $this->module->getFormModel('signup', ['scenario' => SignupForm::SCENARIO_BACKEND]);
        //$model->userIp = Yii::$app->request->userIp;

        if ($model->load(Yii::$app->request->post()) && $model->signup()) {
            Yii::$app->session->setFlash('success', 'User is created successfully. ');

            return $this->redirect(['/user/user']);
        }

        return $this->render($this->action->id, [
            'model' => $model,
        ]);
    }

    public function actionView($id) {
        $model = User::findOne($id);

        return $this->render($this->action->id, [
            'model' => $model,
        ]);
    }

    public function actionDelete($id){

        $model = User::findOne($id);
        $model->delete();
        return $this->redirect(['index']);

    }
	
	public function actionUpdateContact($id) {
		
		$profile = UserProfile::ensureExist($id);
		
		$model = isset($profile->address) ? $profile->address : new Address();
		
		$this->module->configureModel($model, 'contact');

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
			if (!isset($profile->address)) {
				$profile->update(['address_id' => $model->id]);
			}
			Yii::$app->session->setFlash('success', 'Update Contact successfully.');
			return $this->refresh();
        }

		return $this->render($this->action->id, [
            'model' => $model,
			'userId' => $id,
		]);
	}
	
	public function actionUpdateIdentity($id) {
		$model = $this->module->getFormModel('identity', ['userId' => $id]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
			Yii::$app->session->setFlash('success', 'Update Contact successfully.');
			return $this->refresh();
        }

		return $this->render($this->action->id, [
            'model' => $model,
			'userId' => $id,
		]);
	}

	public function actionUpdateConfig($id, $type = 'default') {
        $user = User::findOne($id);
		
        $formModel = $this->module->getFormModel('config', ['user' => $user]);

        if ($formModel->load( Yii::$app->request->post() ) && $formModel->save() ) {
            Yii::$app->session->setFlash('success', 'The user account '. $user->username .' updated. ');

            return $this->redirect(['update', 'id' => $id, 'type' => $type]);
        }
		
        return $this->render($this->action->id, [
            'formModel' => $formModel, 
            'type' => $type,
        ]);
    }
	
	public function actionUpdateProfileData($id) {
		
		$model = UserProfile::findOne(['user_id' => $id]);
		
		if ($model->load(Yii::$app->request->post()) && $model->save()) {
			Yii::$app->session->setFlash('success', 'Update Profile successfully');
			return $this->refresh();
		}

        return $this->render('update-profile-data', [
            'model' => $model,
			'userId' => $id, // Don't get user id value from profile as profile not necessary exist. $profile maybe null.
        ]);
	}

	public function actionUpdate($id)
    {
		$profile = UserProfile::findOne(['user_id' => $id]);
		
		$model = $this->module->getFormModel('profile', [
			'userId' => $id, // Needed when profile is null
			'profile' => $profile,
		]);
		
        /*$profile = UserProfile::findOne(['user_id' => $id]);
        $profile->scenario - $this->module->profileUpdateScenario;
        $profile->setRole();
        
        $address = $profile->address;
        $address->scenario = Address::SCENARIO_NO_REQUIRED;
        $model = new MultiModel([
            'models' => [
                'profile' => $profile,
                'address' => $address,
            ]
        ]);*/

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
			Yii::$app->session->setFlash('success', 'Update Profile successfully');
			return $this->refresh();
        }

        return $this->render('update', [
            'sideNav' => [],
            'model' => $model,
			'userId' => $id, // Don't get user id value from profile as profile not necessary exist. $profile maybe null.
        ]);
    }

    public function actionRole($id) {
        $model = new UserRoleForm(['userId' => $id]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        }

        return $this->render($this->action->id, [
            'model' => $model,
			'userId' => $id,
        ]);
    }

    public function actionActivate($id) {
		$user = User::findOne($id);
		if (isset($user)) {
			if (!$user->activate()) throw new \Exception('Not able to activate user. '.Html::errorSummary($user));
			
			return $this->redirect(['index']);
		}
    }

    public function actionUnactivate($id) {
		$user = User::findOne($id);
		if (isset($user)) {
			if (!$user->unactivate()) throw new \Exception('Not able to unactivate user. '.Html::errorSummary($user));
			
			return $this->redirect(['index']);
		}
    }
 	
	public function actionApprove($id) {
		$user = User::findOne($id);
		if (isset($user)) {
			if (!$user->approve()) throw new \Exception('Not able to approve user. '.Html::errorSummary($user));
			
			return $this->redirect(['index']);
		}
	}
	
	public function actionUnapprove($id) {
		$user = User::findOne($id);
		if (isset($user)) {
			if (!$user->unapprove()) throw new \Exception('Not able to unapprove user. '.Html::errorSummary($user));
			
			return $this->redirect(['index']);
		}
    }
    
    public function actionEmailActivationCode($id) {
        $user = User::findOne($id);
		if (isset($user)) {
			$notification = new \ant\user\notifications\Activation($user);
			
			\Yii::$app->notifier->on(\tuyakhov\notifications\Notifier::EVENT_AFTER_SEND, function($event) {
				if ($event->response === true) {
					\Yii::$app->session->setFlash('success', 'Send activation code success.');
				} else {
					\Yii::$app->session->setFlash('error', 'Send activation code error.');
				}
			});
			\Yii::$app->notifier->send($user, $notification);
			
			return $this->redirect(['index']);
        }
    }

}