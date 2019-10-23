<?php
namespace ant\user\rbac;

use Yii;
use yii\base\Component;

class Role extends Component{

	const ROLE_DEVELOPER 	= 'developer';
	const ROLE_USER 		= 'user';
	const ROLE_MANAGER 		= 'manager';
	const ROLE_SUPERADMIN	= 'superadmin';
	const ROLE_ADMIN 		= 'admin';
	const ROLE_GUEST		= 'guest';
	const ROLE_DEALER		= 'dealer';
	const ROLE_COMPANY		= 'company';

	protected $auth = 'authManager';
	protected $_rule;
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
	
	public function setRule($rule) {
		$this->_rule = $rule;
	}

	public function getRole(){
		return $this->auth->getRole($this->name);
	}

	public function exist() {
		$role = $this->getRole();
		return isset($role);
	}

	protected function _create() {
		$role = $this->auth->createRole($this->name);
		$role->ruleName = $this->_rule;
		return $this->auth->add($role);
	}

	public static function create($name, $rule) {
		$role = new Role($name);
		$role->setRule($rule);
		if (!$role->_create()) throw new \Exception('Failed to create new role: '.$name);
		return $role;
	}
	
	public static function ensureUserRole() {
		$role = self::ensureRole(self::ROLE_USER, \ant\user\rbac\rules\AuthenticatedUserRule::class);
		return $role;
	}
	
	public static function ensureRole($roleName, $ruleName = null) {
		$role = self::get($roleName);
		
		if ($role->exist()) {
			return $role;
		} else {
			return self::create($roleName, $ruleName);
		}
	}
}
