<?php
/**
 * @link http://www.http://inspiren.my//
 * @copyright Copyright (c) 2017 Inspiren MY
 * @license http://www.inspiren.my/license/
 */
namespace ant\user\console\controllers;

use Yii;

use yii\helpers\Console;
use yii\helpers\ArrayHelper;
use ant\console\components\Controller;
use ant\user\models\User;
use ant\rbac\Role;

/**
 * @author Mlax Wong <mlax@inspiren.my>
 * @since 1.0
 */
class UserController extends Controller
{
	public $defaultUser = [];
	
	protected $systemDefaultUser;

    private $_defaultRole = Role::ROLE_DEVELOPER;

    private $_error;

    private $_roles = [];
	
	public function init() {
		parent::init();
		
		if (!isset($this->systemDefaultUser)) {
			$this->systemDefaultUser = [
				[
					'username' => 'developer',
					'auth_key' => 'x_1PysxtikM7oAoVxd1V8X7yLyCEduxi',
					'password_hash' => '$2y$13$dYJYBWSFMU.vd6Q5oXUIDOOz7U1wpyd4qBwFiFurN.xxFlsz44G8q',
					'email' => 'chy1988@antwebstudio.com',
					'role' => Role::ROLE_DEVELOPER,
				],
				[
					'username' => 'admin',
					'auth_key' => 'PT9DGO69lLPVCdiqnAGn25zcwJP3cgR8',
					'password_hash' => '$2y$13$dYJYBWSFMU.vd6Q5oXUIDOOz7U1wpyd4qBwFiFurN.xxFlsz44G8q',
					'email' => env('ADMIN_EMAIL', 'admin@example.com'),
					'role' => Role::ROLE_ADMIN,
				],
				[
					'username' => 'superadmin',
					'auth_key' => 'kKLI_4w71EvCA9dkwasIhzO-1uexzo5_',
					'password_hash' => '$2y$13$dYJYBWSFMU.vd6Q5oXUIDOOz7U1wpyd4qBwFiFurN.xxFlsz44G8q',
					'email' => 'superadmin@example.com',
					'role' => Role::ROLE_SUPERADMIN,
				],
				[
					'username' => 'chy1988',
					'auth_key' => 'W-gKG5VnDEP8Ism06ypZGhF4MNSqCNcL',
					'password_hash' => '$2y$13$WuJJ0p4DxPIJWl7s9QKZyONvLC5qsNH16CYO9JCeyENT6lQx4JIAO',
					'email' => 'chy1988@gmail.com',
					'role' => Role::ROLE_DEVELOPER,
				],
			];
		}
	}

    public function beforeAction($action)
    {
        $version = Yii::getVersion();
        $this->stdout("User Manager (based on Yii v{$version})\n\n");

        return true;
    }

    /**
     * Creates a new user.
     */
    public function actionIndex()
    {
        $this->actionCreate();
    }

    /**
     * Creates a new user.
     */
    public function actionCreate()
    {
        $this->stdout("Create user\n");

        $params = [];

        $params['username'] = $this->prompt('Username:', ['required' => true, 'validator' => function($input, &$error) {
            if (!$this->validUsername($input))
            {
                $error = $this->_error;
                return false;
            }
            return true;
        }]);

        password:

        $password = $this->silentPrompt('Password(hidden):', ['required' => true]);

        $confirmPassword = $this->silentPrompt('Confirm password(hidden):', ['required' => true]);

        if($password != $confirmPassword)
        {
            $this->stdout("Password mismatch.\n");
            goto password;
        }

        $params['password'] = $password;

        $params['email'] = $this->prompt('E-mail:', ['required' => true, 'validator' => function($input, &$error) {
            if (!$this->validEmail($input))
            {
                $error = $this->_error;
                return false;
            }
            return true;
        }]);

        $this->stdout("\nSelect a role:\n");

        $i =0;
        $newArr = [];
        foreach ($this->getRoles() as $index => $role)
        {
            $newArr[] = $role;
            $this->stdout("[" . $i++ . "] - $role->name\n");
        }
         
        $selectedRole = $this->prompt("\nYour choice 0-" . (count($this->getRoles()) - 1) . ' [' . array_search($this->_defaultRole, $this->getRoles()) . ']:', ['required' => true, 'default' => array_search($this->_defaultRole, $this->getRoles()), 'validator' => function($input, &$error) use($newArr) {
            if (!array_key_exists($input, $newArr))
            {
                $error = 'Invalid option.';
                return false;
            }
            return true;
        }]);

        if($this->createUser($params,$newArr[$selectedRole]->name ))
        {
            $this->stdout("\nUser create successfully.\n", Console::FG_GREEN);
        }
    }

    /**
     * Creates a new user.
     */
    public function actionGenerateDefaultUser()
    {
        $this->stdout("Generating default user ... \n");
		
		foreach ($this->systemDefaultUser as $user) {
			if ($this->validUsername($user['username']) && $this->validEmail($user['email'])) {
				$params = $user;
				$params['registered_ip'] = '127.0.0.1';
				unset($params['role']);

				$this->createUser($params, $user['role'], false, true);

				$this->stdout("\nDefault user generate successfully.\n", Console::FG_GREEN);
			} else {
				$this->stdout("\nDefault user already exists.\n");
			}
		}
		
		foreach ($this->defaultUser as $user) {
			if ($this->validUsername($user['username']) && $this->validEmail($user['email'])) {
				$user = ArrayHelper::merge([
					'status' => 2,
					'registered_ip' => '127.0.0.1',
				], $user);
				
				$newUser = new User($user);
				
				if ($newUser->save()) {
					$newUser->afterSignup(Role::ROLE_USER);
					$this->stdout("\nDefault user generate successfully.\n", Console::FG_GREEN);
				} else {
					$this->stdout(\yii\helpers\Html::errorSummary($newUser));
				}
			}
		}
    }

    protected function createUser($params, $role, $generateAuthKey = true, $throwException = false)
    {
        $user = new User($params);

        $user->status = User::STATUS_ACTIVATED;

        if($generateAuthKey) $user->generateAuthKey();

        if (!$user->save()) {
            if ($throwException) throw new \Exception(print_r($user->errors, 1));
            return false;
        }

        $user->afterSignup($role);

        return true;
    }

    protected function validUsername($username)
    {
        $user = User::findByUsername($username);

        if($user) $this->_error = 'Username already exists.';

        return !$user;
    }

    protected function validEmail($email)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->_error = "Invalid email format";

            return false;
        } else {

            $user = User::findByEmail($email);

            if($user) $this->_error = 'E-mail already exists.';

            return !$user;
        }
    }

    protected function getRoles()
    {
        if(empty($this->_roles))
        {
            $this->_roles = Role::getRoles();
        }

        return $this->_roles;
    }
}
?>
