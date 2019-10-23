<?php
namespace ant\rbac;

use Yii;
use yii\web\ForbiddenHttpException;
use ant\rbac\Permission;

class ModelAccessControl extends \yii\filters\AccessControl {
	public $notFoundMessage = 'Page not found';
	public $rules = [
		['allow' => true],
	];
	
	/*
	public function init() {
		$this->model = function($className) {
			if (Yii::$app->request->get('id', false)) {
				return $className::findOne(Yii::$app->request->get('id'));
			} else if (Yii::$app->request->post('id', false)) {
				return $className::findOne(Yii::$app->request->post('id'));
			} else if ($this->strict) {
				throw new \Exception('Cannot get model');
			}
		};
		return parent::init();
	}*/
	
	public function can($action, $model, $params = []) {
		return $this->_checkAccess($action, $model, $params, false);
	}
	
	public function checkExist($model) {
		if (!isset($model)) throw new \yii\web\HttpException(404, $this->notFoundMessage);
	}
	
	// Alias for checkAccess, needed when the controller itself have checkAccess Method
	public function checkAccessFor($action, $model, $params = []) {
		return $this->_checkAccess($action, $model, $params);
	}
	
	public function checkAccess($action, $model, $params = []) {
		return $this->_checkAccess($action, $model, $params);
	}
		
	protected function _checkAccess($action, $model, $params = [], $denyAccess = true) {
		try {
			$this->checkExist($model);
			
			$user = $this->user;
			if (!isset($model) || !($model instanceof \yii\base\Model)) trigger_error('Model is invalid', E_USER_ERROR);
			$className = $model::className();
			
			$allow = Yii::$app->user->can(Permission::of($action, $className)->name, array_merge(['model' => $model], $params));
			
			if (!$allow && $denyAccess) {
				if (YII_DEBUG) throw new ForbiddenHttpException('Permission not allowed: '.Permission::of($action, $className)->name, E_USER_ERROR);
				$this->denyAccess($user);
			}
			return $allow;
		} catch (\Exception $ex) {
			throw $ex;
			trigger_error($ex->getMessage(), E_USER_ERROR);
		}
	}
	
	/*public function beforeAction($action) {
		$model = call_user_func($this->model, $this->modelName);
		$user = $this->user;
		$className = isset($model) ? $model::className() : $this->modelName;
		
		$permission = Permission::of($action->id, $className);
		
		if (YII_DEBUG && !Yii::$app->user->can($permission->name, ['model' => $model])) throw new \Exception($permission->name);
		
		if (Yii::$app->user->can($permission->name, ['model' => $model]) || parent::beforeAction($action) !== false) {
			return true;
		} else {
			
			if ($this->denyCallback !== null) {
				call_user_func($this->denyCallback, null, $action);
			} else {
				$this->denyAccess($user);
			}
			
			return false;
		}
	}*/
	
	protected function denyAccess($user)
    {
        if ($user !== false && $user->getIsGuest()) {
            $user->loginRequired();
        } else {
			if (YII_DEBUG) {
				throw new ForbiddenHttpException(Yii::t('yii', self::className().': You are not allowed to perform this action.'));
			} else {
				throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
			}
        }
    }
}