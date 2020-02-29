<?php
namespace ant\rbac;

use Yii;
use yii\web\ForbiddenHttpException;
use ant\rbac\Permission;

class GlobalAccessControl extends \yii\filters\AccessControl {
	public $ruleConfig = ['class' => 'ant\rbac\AccessRule'];
	public $enabled = true;
	public $extraRules = [];
	
	protected $activateUrl;
	protected $_permission = '';

	public $rules = [
		[
			'controllers' => ['site'],
			'actions' => ['login', 'timezone'], // error should not included, as error will show admin panel layout also.
			'allow' => true,
		],
		[
			'controllers' => ['site'],
			'matchCallback' => [\ant\rbac\GlobalAccessControl::class, 'isAdmin'],
			'allow' => true,
		],
		[
			'controllers' => ['debug/default', 'maintenance'],
			'allow' => true,
		],
		[
			'modules' => ['translatemanager'],
			'matchCallback' => [\ant\rbac\GlobalAccessControl::class, 'isAdmin'],
			'allow' => true,
		],
		[
			'modules' => ['crawler', 'cms', 'imagemanager', 'sandbox', 'gridview'],
			'allow' => true,
		],
		[
			'modules' => ['gii'],
			'allow' => YII_DEBUG,
		],
		[
			'controllers' => ['user/signin', 'user/default', 'user/auth-client'],
			'allow' => true,
		],
		[
			'controllers' => ['user/activation'],
			'roles' => ['@'],
			'allow' => true,
		],
		[
			'controllers' => ['setting'],
			'allow' => true,
		],
	];
	
	public function init() {
		$this->activateUrl = \Yii::$app->user->activateUrl;
		if (is_callable($this->extraRules)) {
			$extraRules = call_user_func($this->extraRules, []);
		} else {
			$extraRules = $this->extraRules;
		}
		$this->rules = array_merge($this->rules, $extraRules);
		return parent::init();
	}

	public function beforeAction($action)
    {
		if (\Yii::$app->request->isConsoleRequest || \Yii::$app instanceof \yii\console\Application || !$this->enabled) {
			return true;
		}

        $user = $this->user;
		$controller = $action->controller;
		$permission = Permission::of($action->id, $controller::className());

		// OR
		$this->_permission = $permission->name;

		if (\Yii::$app->user->can($permission->name) || parent::beforeAction($action) !== false) {
			return true;
		} else {
			//if (YII_DEBUG) throw new \Exception('Access Denied: '.$permission->name.' (User ID: '.$user->id.')');

			if ($this->denyCallback !== null) {
				call_user_func($this->denyCallback, null, $action);
			} else {
				$this->denyAccess($user);
			}

			return false;
		}
	}
	
	public static function isAdmin() {
		return \Yii::$app->user->can('admin');
	}

	protected function notInActivationPage() {
		$activateUrl = (array) $this->activateUrl;
		return $activateUrl[0] !== Yii::$app->requestedRoute;
	}

	protected function activateRequired($checkAjax = true, $checkAcceptHeader = true) {
		$request = \Yii::$app->getRequest();
		$user = \Yii::$app->user;
		$canRedirect = true;
        //$canRedirect = !$checkAcceptHeader || $user->checkRedirectAcceptable();

        if ($user->enableSession
            && $request->getIsGet()
            && (!$checkAjax || !$request->getIsAjax())
            && $canRedirect
        ) {
            $user->setReturnUrl($request->getUrl());
        }
        if ($this->activateUrl !== null && $canRedirect) {
            if ($this->notInActivationPage()) {
                return \Yii::$app->getResponse()->redirect($this->activateUrl);
            }
        }
        throw new ForbiddenHttpException(Yii::t('yii', 'Activate Required'));
	}

	protected function denyAccess($user)
    {
        if ($user !== false && $user->getIsGuest()) {
            $user->loginRequired();
		} else if (!$user->identity->isActive()) {
			$this->activateRequired();
        } else {
			if (YII_DEBUG) {
				throw new ForbiddenHttpException(Yii::t('yii', self::className().': You are not allowed to perform this action. ('.$this->_permission.')'));
			} else {
				throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
			}
        }
    }
}
