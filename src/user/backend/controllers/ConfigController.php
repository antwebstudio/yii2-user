<?php 
namespace backend\modules\user\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\VerbFilter;

use frontend\filters\AccessControl;
use frontend\filters\AccessRule;
use ant\user\models\InviteRequest;
use ant\user\models\UserInvite;
use ant\user\models\UserConfigForm;
use ant\user\models\UserConfig;
use ant\user\models\User;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\base\Model;

class ConfigController extends Controller
{
    /*public function actionIndex()
    {
        $query = UserInvite::find()->andWhere(['is not', 'user_id' , NULL])->andWhere(['or', ['type' => null], ['type' => \backend\modules\user\Module::INVITE_TYPE_ROLE] ]);
        $dataProvider = new ActiveDataProvider(['query' => $query]);
        //$configRecordRows = Yii::$app->getModule('user')->getUserConfigRecords(\backend\modules\user\Module::INVITE_TYPE_ROLE);

        $configRecordRows = [];
        if (isset(Yii::$app->getModule('user')->inviteModel['default']['fields'])) {

            foreach (Yii::$app->getModule('user')->inviteModel['default']['fields'] as $key => $value) {
                $configRecordRows[$key] = Yii::$app->getModule('user')->inviteModel['default']['fields'][$key]['show']['config'];
            }
        }

        $userModule = Yii::$app->getModule('user');

        return $this->render('index',[
            'dataProvider' => $dataProvider ,
        ]);
    }

    public function actionUpdate($id, $inviteType = 'default'){
        $user = User::find()->andWhere(['id' => $id])->one();
        $models = UserConfig::find()->findConfigs($user->id);
        foreach ($models as $key => $model) {
            $models[$model->config_name] = $model;
            unset($models[$key]);
        }

        $formModel = new UserConfigForm ();
        $formModel->inviteType = $inviteType;
        $formModel->models = $models;
        $formModel->userConfigs = $models;
        $formModel->userId = $user->id;
        $formModel->currentRole = $formModel->getRole($user->id)->name;
        $formModel->rolesChoice = $formModel->rolesDropDown;

        if ($formModel->load( Yii::$app->request->post() ) && $formModel->save() ) {
            Yii::$app->session->setFlash('success', 'The user account '. $user->username .' updated. ');

            return $this->redirect(['update', 'id' => $id, 'inviteType' => $inviteType]);
        }

        return $this->render('update', [
            'formModel' =>  $formModel, 
            'id' => $id, 
            'inviteType' => $inviteType
        ]);
    }*/

    public function actionMain($id) {
        $model = $this->module->getFormModel('config', ['userId' => $id]);

        if ($model->load( Yii::$app->request->post()) && $model->save() ) {
            Yii::$app->session->setFlash('success', 'The user config are updated. ');
        }

        return $this->render($this->action->id, [
            'model' => $model,
        ]);
    }
}
