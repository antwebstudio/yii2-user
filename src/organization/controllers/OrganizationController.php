<?php
namespace ant\organization\controllers;

use Yii;
use ant\organization\models\Organization;

class OrganizationController extends \yii\web\Controller {
	public function actionUpdateOwn() {
		$organization = Organization::find()->haveCollaborator(Yii::$app->user->id)->one();

		return $this->render($this->action->id, [
			'model' => $organization,
		]);
	}
}