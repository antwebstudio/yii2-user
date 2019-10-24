<?php 
namespace backend\modules\user\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\helpers\Html;

use frontend\filters\AccessControl;
use frontend\filters\AccessRule;
use ant\user\models\UserInvite;
use yii\data\ActiveDataProvider;
use ant\rbac\Role;
use ant\rbac\ModelAccessControl;
use ant\user\models\UserInviteSearch;
use ant\user\models\User;
use yii\helpers\ArrayHelper;

class InviteController extends Controller
{
    public function behaviors()
    {
        return [
            'access' =>
            [
                'class' => ModelAccessControl::className(),
            ],
            'verbs' =>
            [
                'class' => VerbFilter::className(),
                'actions' =>
                [
                    'delete' => ['POST'],
                    'resend' => ['POST'],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        $model = UserInvite::find();
        // $dataProvider = new ActiveDataProvider([
        //     'query' => $model,
        //     //'sort' => ['attributes' => ['data.firstname']],
        //     ]);
        $searchModel = new UserInviteSearch(
             );
        $dataProvider = $searchModel->search( Yii::$app->request->queryParams);
        $model = new UserInvite();
        return $this->render('index',['dataProvider' => $dataProvider , 'model' => $model ] );
    }

    public function actionUpdate($id, $inviteType = 'default') {

        $model = UserInvite::findOne($id);
		
        if (!isset($model)) throw new \Exception('Invite Request does not find with the ID' . $id);
		
        $model = $this->module->getFormModel('createInvite', [
			'inviteType' => $inviteType,
			'userInvite' => $model,
		]);
		
		if ($model->load (Yii::$app->request->post()) && $model->save()) {
			Yii::$app->session->setFlash('success', 'Invite account email '. $model->getModel('userInvite')->email .' has been updated.');
			return $this->redirect(['update', 'inviteType' => $inviteType, 'id' => $id]);
		}
        
        return $this->render('update', ['model' => $model]);
    }

    public function actionDelete($id) {

        $model = UserInvite::findOne($id);
        $model->delete();
        return $this->redirect(Yii::$app->request->referrer);

    }

    public function actionResend($id, $inviteType = 'default')
    {
		
        $model = UserInvite::findOne($id);
		
        $model = $this->module->getFormModel('createInvite', [
			'inviteType' => $inviteType,
			'userInvite' => $model,
		]);
		
        $model->scenario = UserInvite::SCENARIO_RESEND;

        if ($model->sendInvite()) {
            Yii::$app->session->setFlash('success', 'Check the email for further instructions.');
        } else {
            Yii::$app->session->setFlash('error', 'Resend error.');

        }

        return $this->redirect(Yii::$app->request->referrer);
    }

    public function actionCreate($inviteType = 'default'){
        $model = $this->module->getFormModel('createInvite', ['inviteType' => $inviteType]);

        return $this->render('create', ['model' => $model]);
    }
    
    public function actionSend($inviteType = 'default'){
        $model = $this->module->getFormModel('createInvite', ['inviteType' => $inviteType]);

        if ($model->load(Yii::$app->request->post()) && $model->sendInvite()) {
			Yii::$app->session->setFlash('success', 'Check the email for further instructions.');
			return $this->redirect(['create', 'inviteType' => $inviteType]);
        }
		return $this->render('create', ['model' => $model]);
    }

}