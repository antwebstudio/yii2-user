<?php
namespace ant\user\controllers;

use Yii;
use yii\web\Controller;

class DashboardController extends \yii\web\Controller
{
	public $layout = '//member-dashboard';
	
	public function actionIndex() {
		return $this->render($this->action->id, [
		]);
	}
}