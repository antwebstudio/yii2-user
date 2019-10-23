<?php
namespace ant\rbac;

use Yii;
use yii\base\Component;

class Role extends Component {

	const ROLE_DEVELOPER 	= 'developer';
	const ROLE_USER 		= 'user';
	const ROLE_MANAGER 		= 'manager';
	const ROLE_SUPERADMIN	= 'superadmin';
	const ROLE_ADMIN 		= 'admin';
	const ROLE_GUEST		= 'guest';
	const ROLE_DEALER		= 'dealer';
	const ROLE_COMPANY		= 'company';

	protected $auth = 'authManager';
	private $name = '';

	public function __construct($name){
		$this->auth = \Yii::$app->get('authManager');
		$this->name = $name;
	}

	public static function get($name){
		return new Role($name);
	}

	public function assign($user){
		$this->auth->assign($this->getRole(), $user->getId());
	}

	public static function getRoles(){
		return \Yii::$app->get('authManager')->getRoles();
	}

	public function revoke($user) {
		$this->auth->revoke($this->getRole(), $user->getId());
	}

	public function __get($key){
		return $this->getRole()->{$key};
	}

	public function __set($key, $value){
		$this->getRole()->{$key} = $value;
	}

	public function getRole(){
		return $this->auth->getRole($this->name);
	}

	public function exist() {
		$role = $this->getRole();
		return isset($role);
	}

	protected function _create() {
		return $this->auth->add($this->auth->createRole($this->name));
	}

	public static function create($name) {
		$role = new Role($name);
		if (!$role->_create()) throw new \Exception('Failed to create new role: '.$name);
		return $role;
	}
}
