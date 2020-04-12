<?php

namespace ant\user\models;

use Yii;
use yii\behaviors\BlameableBehavior;
use yii\base\NotSupportedException;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\rbac\DBManager;
use yii\helpers\Html;

use ant\behaviors\TimestampBehavior;
use ant\rbac\Role;
use ant\user\models\UserProfile;
use ant\user\models\query\UserQuery;
use ant\address\models\Address;
use ant\token\models\Token;
use ant\user\models\User;

/**
 * This is the model class for table "{{%user_invite}}".
 *
 * @property string $id
 * @property string $email
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 * @property string $created_by
 * @property string $updated_by
 * @property string $token_id
 *
 * @property Token $token
 */
/**

     * @status Status group that define user that still available to acces system.
     */

class UserInvite extends \yii\db\ActiveRecord
{
    const INVITE_TYPE_ROLE = 'role';
    const INVITE_PROFILE_SETTING = 'profileSetting';
    const INVITE_SHOW_FORM = 'showForm';
    const INVITE_SHOW_CONFIG = 'showConfig';
    const INVITE_TYPE_PROJECT = 'project';
	
    const RESET_KEY_LENGTH = 40;
    /**
    * @status User status not active.
     */
    const STATUS_NOT_ACTIVATED = 1;
    /**
     * @status User status actived.
     */
    const STATUS_ACTIVATED = 2;
    /**
     * @status Status for deleted user.
     */
    const STATUS_DELETED = 3;

    const SCENARIO_DEFAULT = 'default';
    const SCENARIO_RESEND  = 'resend';
    const SCENARIO_UPDATE  = 'update';

    public $inviteModelType = 'default';
	
	protected $_emailFrom;
    protected $_roles = [];

    public function getRoles(){
        $roles = Role::getRoles();
        foreach ($roles as $role => $value) {
            if ($role == 'admin' || $role == 'user') {
                $this->_roles[$role] = $role;
            }
        }
        return $this->_roles;
    }

    public static function generateInviteKey()
    {
        return Yii::$app->security->generateRandomString(self::RESET_KEY_LENGTH);
    }

    public function behaviors()
    {
        return [
			'configurable' => [
				'class' => 'ant\behaviors\ConfigurableModelBehavior',
			],
            ['class' => TimestampBehavior::className()],
            ['class' => BlameableBehavior::className()],
			[
				'class' => 'ant\behaviors\SerializeBehavior',
				'attributes' => ['data'],
				'serializeMethod' => \ant\behaviors\SerializeBehavior::METHOD_JSON,
			]
        ];
    }
    /**
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user_invite}}';
    }

    /**
     * @inheritdoc
     */

    public function rules()
    {
        $rules = $this->getCombinedRules([
            [['email','role'], 'required'],
            [['status', 'created_by', 'updated_by', 'token_id'], 'integer'],
            [['data', 'created_at', 'updated_at', 'type'], 'safe'],
            [['email'], 'string', 'max' => 255],
            [['email'], 'unique', 'on' => [ self::SCENARIO_DEFAULT , self::SCENARIO_RESEND , self::SCENARIO_UPDATE ]],
            [['email'],'email'],
            // only scenario is user invite only can use, when create new user invite.
            ['email', 'unique', 
                'targetClass' => '\ant\user\models\User', 
                'message' => 'This email address has already been taken.' , 
                'on' => self::SCENARIO_DEFAULT 
            ],
            ['email', function ($attribute, $params, $validator) {
                if(!(self::find()
                    ->andWhere(['email' => $this->email])
                    ->andWhere(['status' => self::STATUS_NOT_ACTIVATED])
                    ->count() > 0)) 
                {
                    $this->addError($attribute, 'Invalid email address.');
                } 
            }, 'on' => self::SCENARIO_RESEND],
            [['token_id'], 'exist', 'skipOnError' => true, 'targetClass' => Token::className(), 'targetAttribute' => ['token_id' => 'id']],
        ]);
        
        return $rules;
    }

    public function sendInvite()
    {
        if(!$this->save()) return false;
        
        $tokens = Token::find()
            ->byUserInvite($this)
            ->byType(Token::TOKEN_TYPE_USER_INVITE)
            ->all();

        foreach($tokens as $token) $token->delete();

        $token = Token::create($this, Token::TOKEN_TYPE_USER_INVITE,  [
            'tokenkey' => Token::createTokenKey(),
            'email' => $this->email,
        ]);

        Yii::$app->mailer->compose(
            ['text' => '@ant/user/mails/inviteRequestToken-text', 'html' => '@ant/user/mails/inviteRequestToken-html'],
            [
                'tokenQueryParams' => $token->queryParams
            ])
            ->setFrom([$this->emailFrom => Yii::$app->name])
            ->setTo($this->email)
            ->setSubject('User invite for ' . Yii::$app->name)
            ->send();

        return true;
    }
	
	public function setEmailFrom($value) {
		$this->_emailFrom = $value;
	}

    public function getEmailFrom() 
    {
		if (isset($this->_emailFrom)) return $this->_emailFrom;
        return env('ROBOT_EMAIL');
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'email' => 'Email',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'created_by' => 'Created By',
            'updated_by' => 'Updated By',
            'token_id' => 'Token ID',
            'role' => 'Role',
            'data' => 'Data',

        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTokens()
    {
        return $this->hasOne(Token::className(), ['id' => 'token_id']);
    }

    public function getUser(){
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}