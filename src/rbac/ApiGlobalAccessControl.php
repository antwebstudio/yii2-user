<?php
namespace ant\rbac;

use ant\rbac\Permission;

class ApiGlobalAccessControl extends  \yii\filters\auth\CompositeAuth {
    public function beforeAction($action)
    {
		$controller = $action->controller;
		$permission = Permission::of($action->id, $controller::className());
        if (\Yii::$app->user->can($permission->name) || parent::beforeAction($action) !== false) {
            return true;
        }
        throw new \yii\web\UnauthorizedHttpException('Your request was made with invalid credentials.');
    }
}