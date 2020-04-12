<?php
namespace ant\user\models;

use Yii;
use yii\base\InvalidParamException;
use yii\base\Model;

use ant\user\models\UserInvite;
use ant\user\models\InviteRequest;
use ant\token\models\Token;
use ant\user\models\User;
use ant\user\models\UserConfig;
use ant\rbac\Role;
use ant\user\models\AdvancedSignupForm;

/**
 * Create invite form
 */
class CreateInviteForm extends AdvancedSignUpForm
{

    protected $_token;
    protected $_inviation;
    protected $_user;
	protected $loaded;
	
	protected $_modelData = [];
	
	public $inviteDataFields;
	public $inviteType;
    public $tokenkey;
	public $roleName;
	public $skipTokenValidation = false;
	public $userConfig = [];

    public function init(){
        if  (
            empty($this->tokenkey) 
            ||  !is_string($this->tokenkey) 
            ||  empty($this->email) 
            ||  !is_string($this->email) 
            ) 
        throw new InvalidParamException('Account create key cannot be blank.');
        $this->_inviation = UserInvite::find()->andWhere(['email' => $this->email])->one();
        
		//$this->validateToken();
		
		$this->autofillSignupFieldWithInviteData();

        parent::init();
    }
	
	public function validateToken() {
		
        if(!$this->_inviation) return false;

		if (!$this->skipTokenValidation) {
			$this->_token = Token::find()
				->byUserInvite($this->_inviation)
				->byType(Token::TOKEN_TYPE_USER_INVITE)
				->byQueryParams([
					'tokenkey' => $this->tokenkey,
					'email' => $this->email,
				])
				->one();
			if (!isset($this->_token)) {
				 return false;
			}
		}
		return true;
	}
	
	public function signup($role = Role::ROLE_USER, $status = User::STATUS_NOT_ACTIVATED, $email = null, $signUpType = parent::SIGN_UP_TYPE_DEFAULT){
		if (!$this->validateToken()) throw new InvalidParamException('Wrong account invite detail.');
		
        $transaction = Yii::$app->db->beginTransaction();
		try {
			
			// If the invited user signup using the email address which receiving the invitation email, that mean it is a valid email, and hence mark user as activated.
			if ($this->email == $this->user->email) {
				$this->defaultUserStatus = User::STATUS_ACTIVATED;
			}
			
			$result = parent::signup($this->roleName, $status, $this->email);
			if($result)
			{
				//throw new \Exception($this->signupType);
				//if ($this->signupType == 'invite') {
					$model = UserInvite::find()->andWhere(['email' => $this->email])->one();
					
					if (!isset($model)) throw new \Exception('Something is wrong. ');
					$model->scenario = UserInvite::SCENARIO_UPDATE;
					//throw new \Exception('test');
					$model->status = UserInvite::STATUS_ACTIVATED;
					$model->user_id = $this->user->id;
					
					if (!$model->save()) {
						throw new \Exception('Failed to update invite user status. ');
					}
				
					foreach ($this->userConfig as $configName => $value) {
						UserConfig::set($this->user->id, $configName, $value);
					}
			
				if (isset($this->_token)) {
					$this->_token->delete();
				}
			}
			$transaction->commit();
			return $result;
		} catch (\Exception $ex) {
			$transaction->rollback();
			throw $ex;
		}
    }

    protected function getDbFieldNameWithConfigfieldName($configFieldActualName){
        // example : profile['firstname']
        $stringOfArrayFieldName = explode("[", $configFieldActualName);
        return $stringOfArrayFieldName[0];
    }
	
	/*public function getModel($key, $numberOfInstance = 1, $refresh = false) {
		$model = parent::getModel($key, $numberOfInstance, $refresh);
		if (isset($this->_modelData[$key]) && !$this->loaded) {
			$model->attributes = $this->_modelData[$key];
		}
		return $model;
	}*/
	
	public function rules() {
		$rules = parent::rules();
		$rules[] = ['userConfig', 'safe'];
		return $rules;
	}
	
	public function load($data, $formName = null) {
		$this->loaded = parent::load($data, $formName);
		//throw new \Exception($this->email.print_r($data,1).$this->getModel('user')->email);
		return $this->loaded;
	}

    protected function autofillSignupFieldWithInviteData(){
		//$userInvite = UserInvite::find()->andWhere(['email' => $this->email])->one();
		$userInvite = $this->_inviation;
		
		$this->roleName = $userInvite->role;
		
		//$this->_modelData = $userInvite->data;
		
		foreach ($userInvite->data as $name => $value) {
			if (!in_array($name, ['userConfig'])) {
				$this->getModel($name)->attributes = $value;
			}
		}
		//$this->getModel('profile');
		$this->getModel('user')->email = $this->email;

		$this->userConfig = isset($userInvite->data['userConfig']) ? $userInvite->data['userConfig'] : [];
		
		/*foreach ((array) $userInvite->data as $attribute => $data) {
			//$this->_modelData = 
			if (is_object($this->getModel($attribute))) {
				//$this->getModel($attribute)->attributes = $data;
			} else {
				//$this->{$attribute} = $data;
			}
		}*/
        /*foreach ((array) $userInvite->data as $dbInviteConfigName => $value) {
            if (isset($this->inviteDataFields) ) {
                foreach ($this->inviteDataFields as $configFieldActualName => $arrayConfigFields) {
                    $dbConfigName = $this->getDbFieldNameWithConfigfieldName($configFieldActualName);
                if ($this->hasProperty($dbConfigName) ) {
                    $this->{$dbConfigName}[$dbInviteConfigName] = $value;
                }
                    if ($arrayConfigFields['field']['type' ] == 'widget' ){
                        if (is_array($userInvite->data[$dbInviteConfigName]) && $arrayConfigFields['field']['widgetClass'] == MultipleInput::className()  ) {
                            $this->processFillDataForWidgetMultipleInput($userInvite, $this->inviteType, $configFieldActualName, $dbInviteConfigName);
                        }
                        else {
                            //other type of inputWidget
                        }
                    }
                }
            }
        }*/
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'password'=> 'Password',
            'confirmPassword'=> 'Confirm Password'
        ];
    }
}