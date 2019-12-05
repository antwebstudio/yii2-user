<?php
namespace ant\collaborator\backend\controllers;

use Yii;
use yii\helpers\Url;
use yii\data\ActiveDataProvider;
use ant\collaborator\models\CollaboratorUser;
use ant\collaborator\models\CollaboratorGroupMap;
use ant\collaborator\models\AddCollaboratorForm;

class CollaboratorGroupController extends \yii\web\Controller {
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => \yii\filters\VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionManage($id) {
        $model = new AddCollaboratorForm(['collaboratorGroup' => $id]);

        $dataProvider = new ActiveDataProvider([
            'query' => CollaboratorUser::find()->alias('user')
                ->addSelect(['*', 'map.id as collaboratorGroupMapId'])
                ->leftJoin('{{%collaborator_group_map}} map', 'map.user_id = user.id')
                ->andWhere(['map.collaborator_group_id' => $id]),
        ]);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(Url::current());
        }

        return $this->render($this->action->id, [
            'dataProvider' => $dataProvider,
            'model' => $model,
        ]);
    }

    public function actionAjaxUsers($q) {
        \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
        
        return CollaboratorUser::find()->andWhere(['like', 'username', $q])
            ->asArray()->all();
    }

    public function actionDeleteCollaborator($id) {
        $model = CollaboratorGroupMap::findOne($id);
        $model->delete();
        return $this->redirect(['/collaborator/backend/collaborator-group/manage', 'id' => $model->collaborator_group_id]);
    }
}