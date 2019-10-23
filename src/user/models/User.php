<?php
namespace ant\user\models;

use Yii;
use yii\base\NotSupportedException;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\rbac\DBManager;
use tuyakhov\notifications\NotifiableTrait;
use tuyakhov\notifications\NotifiableInterface;

use ant\behaviors\TimestampBehavior;
use ant\behaviors\AttachBehaviorBehavior;
use ant\rbac\Role;
use ant\user\models\UserProfile;
use ant\user\models\query\UserQuery;
use ant\address\models\Address;
use ant\token\models\Token;
use ant\collaborator\models\CollaboratorGroup;


/**
 * User model
 *
 * @property integer $user_id
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string $email
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property integer $logged_at
 *
 * @property string $password write-only password
 * @property yii/rbac/Roles[] $roles
 * @property ant\user\models\UserProfile $userProfile
 */
class User extends ActiveRecord implements IdentityInterface, NotifiableInterface
{
    use NotifiableTrait;
	
    const SCENARIO_NO_REQUIRED_EMAIL = 'no_required_email';
    const SCENARIO_EMAIL_AS_USERNAME = 'emaili_as_username';

    /**
     * @status User status not active.
     */
    const STATUS_NOT_ACTIVATED = 1;
    /**
     * @status User status actived.
     */
    const STATUS_ACTIVATED = 2;
    /**
     * @status Status to force user change password.
     */
    const STATUS_CHANGE_PASSWORD = 3;
    /**
     * @status Status for deleted user.
     */
    const STATUS_DELETED = 4;
    /**
     * @status Status group that define user that still available to acces system.
     */
    const STATUS_ALIVE =
    [
        self::STATUS_NOT_ACTIVATED,
        self::STATUS_ACTIVATED,
        self::STATUS_CHANGE_PASSWORD,
    ];
    /**
     * @status Status group that define user deleted or not available to access system.
     */
    const STATUS_DEAD =
    [
        self::STATUS_DELETED,
    ];
    /**
     * @event Event after signup
     */
    const EVENT_AFTER_SIGNUP = 'afterSignup';
    /**
     * @event Event after login
     */
    const EVENT_AFTER_LOGIN = 'afterLogin';


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @return UserQuery
     */
    public static function find()
    {
		$user = new User;
		if ($user->hasAttribute('app_id')) {
			return (new UserQuery(get_called_class()))->andWhere(['app_id' => env('APP_ID')	]);
		} else {
			return new UserQuery(get_called_class());
		}
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        $behaviors = [
			'configurable' => [
				'class' => 'ant\behaviors\ConfigurableModelBehavior',
			],
			AttachBehaviorBehavior::DEFAULT_NAME => [
                'class' => AttachBehaviorBehavior::className(),
                'config' => '@common/config/behaviors.php',
            ],
            [
                'class' => TimestampBehavior::className()
            ],
            [
                'class' => \ant\behaviors\IpBehavior::className(),
                'createdIpAttribute' => 'registered_ip',
                'updatedIpAttribute' => false,
				'preserveNonEmptyValues' => true,
            ]
        ];
		
		if (class_exists('ant\member\behaviors\MemberBehavior')) {
			$behaviors[] = 'ant\member\behaviors\MemberBehavior';
		}
		
		return $behaviors;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return
        [
            [['username', 'email'], 'required', 'on' => ['default', self::SCENARIO_EMAIL_AS_USERNAME]],
			[['username', 'email'], 'filter', 'filter' => 'strtolower'],
            [['username', 'status'], 'required', 'on' => self::SCENARIO_NO_REQUIRED_EMAIL],
            [['username'], 'match', 'pattern' => '/^[a-z]\w*$/i', 'when' => function($model) {
                return strpos($model->username, '@') === false;
            }],
            [['username'], 'email', 'when' => function($model) {
                return strpos($model->username, '@') !== false;
            }],
            ['username', 'compare', 'compareAttribute' => 'email', 'message' => 'Username and email not match', 'on' => self::SCENARIO_EMAIL_AS_USERNAME],
			
			['email', 'string', 'max' => 255],
            ['email', 'unique', 'message' => 'This email address has already been taken.'],
            ['email', 'trim'],
			
            [['id', 'status'], 'integer'],
            [['username', 'password_hash', 'email'], 'string', 'min' => 1, 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            ['status', 'default', 'value' => self::STATUS_NOT_ACTIVATED],
            ['status', 'in', 'range' =>
            [
                self::STATUS_NOT_ACTIVATED,
                self::STATUS_ACTIVATED,
                self::STATUS_CHANGE_PASSWORD,
                self::STATUS_DELETED
            ]],
        ];
    }
	
	public function attributeLabels() {
		return $this->getCombinedAttributeLabels([]);
	}

    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        return static::find()
            ->alive()
            ->andWhere(['id' => $id])
            ->one();
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public function getFullname() {
        return isset($this->profile) ? $this->profile->fullname : null;
    }

    // Name display in email when sending email to this user
    public function getEmailDisplayName() {
        return isset($this->fullname) ? $this->fullname : $this->username;
    }

	public function getPublicIdentity() {
		return $this->username;
	}
	
	public function getDefaultEmail() {
		return $this->email;
	}

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
		return static::findOne(['auth_key' => $token]);
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::find()
            ->alive()
            ->andWhere(['username' => $username])
            ->one();
    }

    /**
     * Find user by email
     *
     * @param  string $email
     * @return static|null
     */
    public static function findByEmail($email)
    {
        return static::find()
            ->alive()
            ->andWhere(['email' => $email])
            ->one();
    }

    /**
     * Find user by username or email
     *
     * @param  string $login
     * @return static|null
     */
    public static function findByLogin($login)
    {
        return static::find()
            ->alive()
            ->andWhere(['or', ['username' => $login], ['email' => $login]])
            ->one();
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }
	
	public function generateActivationToken() {
		$tokenIds = Token::find()
			->alias('token')
			->select('token.id')
			->byUser($this)
			->byType(Token::TOKEN_TYPE_USER_ACTIVATION)
			->asArray()->all();
			
		//foreach ($previousTokens as $token) $token->delete();
		Token::deleteAll(['id' => \ant\helpers\ArrayHelper::getValues($tokenIds, 'id')]);

		return Token::create($this, Token::TOKEN_TYPE_USER_ACTIVATION, [
			'code' => \Yii::$app->security->generateRandomString(Token::ACTIVATION_CODE_LENGTH),
			'email' => $this->email,
		]);
	}

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Get user assigned roles
     *
     * @return \yii\rbac\Role[]
     */
    public function getRoles(){
        $auth = \Yii::$app->get('authManager');
        return $auth->getRolesByUser($this->getId());
    }
	
	public function getAvatar($default) {
		if (isset($this->profile)) {
			return $this->profile->getAvatar($default);
		}
		return $default;
	}

    /**
     * Get user profile
     *
     * @return commmon\modules\user\models\UserProfile
     */
    public function getProfile()
    {
         return $this->hasOne(UserProfile::className(), ['user_id' => 'id'])->andOnCondition(['main_profile' => 1]);
    }

    public function getProfiles($excludeMain = true)
    {
        $relation = $this->hasMany(UserProfile::className(), ['user_id' => 'id']);

        if($excludeMain) $relation->andOnCondition(['main_profile' => 0]);

        return $relation;
    }

    public function getTokens()
    {
        return $this->hasMany(Token::className(), ['id' => 'token_id'])->viaTable('{{%user_token_map}}', ['user_id' => 'id']);
    }

    public function getInvite() {
        return $this->hasOne(UserInvite::className(), ['user_id' => 'id']);
    }

    public function beforeSave($insert) {
        if (!isset($this->username)) {
            $this->username = $this->email;
        }
        return parent::beforeSave($insert);
    }

    /**
     * Creates user profile and application event
     *
     * @param string $roleName
     * @param array $profileData
     */
    public function afterSignup($roleName, array $profileData = [])
    {
        $this->refresh();

        $profile = new UserProfile();
        $profile->main_profile = true;
        $profile->load($profileData, '');
        $this->link('profile', $profile);

		// Revoke all assignments first
		\Yii::$app->authManager->revokeAll($this->id);
		
		// Assign new role for new user
        $role = Role::get($roleName);
		if ($role->exist()) {
			$role->assign($this);
		} else {
			Role::create($roleName)->assign($this);
		}
		
        $this->trigger(self::EVENT_AFTER_SIGNUP);
    }

    /**
     * TRASH
     */

    /**
     * Generates new password reset token
     */
    /*public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }*/

    /**
     * Removes password reset token
     */
    /*public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }*/

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    /*public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVATED,
        ]);
    }*/

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return boolean
     */
    /*public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }*/

    /*protected function tep_validate_password($plain, $encrypted) {
        if (isset($plain) && isset($encrypted)) {
            // split apart the hash / salt
            $stack = explode(':', $encrypted);

            if (sizeof($stack) != 2) return false;
            if (md5($stack[1] . $plain) == $stack[0]) {
                return true;
            }
        }

        return false;
    }*/

    /*public function getUsername() {
        return $this->username;
    }

    public function getEmail() {
        return  $this->email;
    }*/

    /*
    public function getRoles(){
        $auth = \Yii::$app->get('authManager');
        return $auth->getRolesByUser($this->getId());
    }*/
	
	public function getIsApproved() {
		return $this->is_approved;
	}

    public function getIsActive() {
        return $this->isActive();
    }

    public function getIsSignupByInvite() {
        return isset($this->invite);
    }
	
	public function getIdentityId() {
		return $this->hasMany(\ant\user\models\UserIdentity::className(), ['user_id' => 'id']);
	}

    public function isActive(){
        return $this->status != self::STATUS_NOT_ACTIVATED;
    }

    public function activate() {
        $this->status = self::STATUS_ACTIVATED;
        return $this->save();
    }

    public function unactivate() {
        $this->status = self::STATUS_NOT_ACTIVATED;
        return $this->save();
    }
	
	public function unApprove() {
		$this->is_approved = 0;
        $this->approved_at = new \yii\db\Expression('NULL');
		
		return $this->save();
	}

    public function approve()
    {
		$this->is_approved = 1;
        $this->approved_at = new \yii\db\Expression('NOW()');

        return $this->save();
    }

    /*
    public function isActive(){
        return $this->status == self::STATUS_ACTIVATED;
    }
     */

    /*public static function notActive($username){
        $user = static::findByUsername($username);

        if($user)
            return $user->status == self::STATUS_NOT_ACTIVATED;
        else
            return false;
    }*/

     /*public static function activeById($id, $activationKey){
        return static::find()
            ->alive()
            ->andWhere(['user_id' => $id, 'activation_key' => $activation_key])
            ->one();
    }*/

    //date 2/8/2018
    public function getCurrentRole() {
        $roles = $this->getRoles();
        $roleInfo = end($roles);
        $roleName = $roleInfo->name;
        return $roleName;
    }
}
