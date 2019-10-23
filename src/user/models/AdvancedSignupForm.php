<?php
namespace ant\user\models;

use Yii;
use yii\base\InvalidParamException;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\db\ActiveRecord;

use ant\user\models\UserInvite;
use ant\token\models\Token;
use ant\user\models\User;
use ant\user\models\UserConfig;
use ant\rbac\Role;

/**
 * Create invite form
 */
class AdvancedSignupForm extends SignupForm
{
    const SIGN_UP_TYPE_DEFAULT = 'default';
    const SIGN_UP_TYPE_ADVANCED = 'advanced';
    const SIGN_UP_TYPE_INVITE = 'invite';

    public $sendActivationEmail = false; //orverride the value of parent property
    public $needEnterPasswordTwice = true;

    //public $profile = [];
    public $config = [];
    public $profileAddress = [];
    public $customFields = [];
	public $positionTitle;
	
	public function behaviors() {
		return \yii\helpers\ArrayHelper::merge(parent::behaviors(), [
			[
				'class' => 'ant\behaviors\EventHandlerBehavior',
				'events' => [
					self::EVENT_BEFORE_VALIDATE => function($event) {
						$this->profile->main_profile = 1;
						$this->contact->email = $this->user->email;
					},
					
					self::EVENT_BEFORE_SAVE => function($event) {
						if (!isset($this->profile->email)) $this->profile->email = $this->user->email;
						if (!isset($this->profile->contact_number)) $this->profile->contact_number = $this->contact->contact_number;
						if (!isset($this->profile->firstname)) $this->profile->firstname = $this->contact->firstname;
						if (!isset($this->profile->lastname)) $this->profile->lastname = $this->contact->lastname;
					}
				],
			],
		]);
	}
	
	public function models() {
		return ArrayHelper::merge(parent::models(), [
			'contact' => [
				'class' => 'ant\contact\models\Contact',
			],
			'profile' => [
				'class' => 'ant\user\models\UserProfile',
				'on '.ActiveRecord::EVENT_BEFORE_INSERT => function($event) {
					$profile = $event->sender;
					//$profile->main_profile = 1;
					//$profile->email = $this->user->email;
                    $profile->user_id = $this->user->id;
					$profile->contact_id = $this->contact->id;
				},
				'on '.\yii\db\ActiveRecord::EVENT_AFTER_INSERT => function($event) {
					//$model = $event->sender;
					/*$this->attributes = [
						'contact_number' => $this->profile->contact_number,
						'email' => $this->profile->email,
						'firstname' => $this->profile->firstname,	
						'lastname' => $this->profile->lastname,
					];*/
					//$model->link('contact', $this->contact);
				},
			],
		]);
	}

    public function beforeValidate() {
        if (!$this->needEnterPasswordTwice && !isset($this->confirmPassword)) $this->confirmPassword = $this->password;
        return parent::beforeValidate();
    }

    protected function getModelFields() {
        if (isset(Yii::$app->getModule('user')->signUpModel[$this->signupType]['fields'])) {
            return Yii::$app->getModule('user')->signUpModel[$this->signupType]['fields'];
        } else {
            return $this->customFields;
        }
    }

    public function rules(){
        $rules = parent::rules();
        $rules[] = ['positionTitle', 'safe'];
        $rules[] = ['config', 'safe'];
        $rules[] = ['profileAddress', 'safe'];
        $rules[] = ['user', 'safe'];

        $serialFieldRules = [];
        $modelFieldRules = [];
        $arraysFieldsRule = [];
        $arraysFieldsRule2 = [];

        $attributeNames = [];
        foreach ($this->modelFields as $fieldName => $fieldArray) {
            // example : $fieldName = profile['firstname']
            $stringOfArrayFieldName = explode("[", $fieldName);
            if (count($stringOfArrayFieldName) <= 2) {
                $fieldName = $stringOfArrayFieldName[0];
                if (isset($fieldArray['field']['rules'])) {
                    $arraysFieldsRule[$fieldName] = ArrayHelper::merge($fieldArray['field']['rules'], isset($arraysFieldsRule[$fieldName]) ? $arraysFieldsRule[$fieldName] : [] );
                }
            } else { // strucutre of two array example : profile['data']['company_birth_date']
                if (isset($fieldArray['field']['rules'])) {
                    $twoLayerModelName = $stringOfArrayFieldName[count($stringOfArrayFieldName) - 3];
                    $fieldName = $stringOfArrayFieldName[count($stringOfArrayFieldName) - 2];
                    $fieldName = str_replace(']', '', $fieldName);
                    $attributeNames = [$fieldName => $fieldName]; // checking use
                    // take ['data'] as validate attribute , 
                    $arraysFieldsRule2[$fieldName] = ArrayHelper::merge($fieldArray['field']['rules'], isset($arraysFieldsRule2[$fieldName]) ? $arraysFieldsRule2[$fieldName] : [] );
                }
            }
        }
        foreach ($arraysFieldsRule as $arrayFieldName => $arrayFieldsRule) {
            foreach ($arrayFieldsRule as $fieldRule) {
                $serialFieldRules[$arrayFieldName][] = $fieldRule;
            }
        }
        if (isset($arraysFieldsRule2)) {
            foreach ($arraysFieldsRule2 as $arrayFieldName2 => $fieldRules) {
                foreach ($fieldRules as $fieldRule) {
                    $modelFieldRules[$arrayFieldName2][] = $fieldRule;
                }
            }
        }
    
        if ($serialFieldRules != null) {
            foreach ($serialFieldRules as $fieldName => $fieldRules) {
                $rules[] = [$fieldName, \ant\validators\SerializableDataValidator::className(), 'rules' => $fieldRules];
            }
        }
        if ($modelFieldRules != null) {
            $rules[] = [$twoLayerModelName, \ant\validators\ModelsValueValidator::className(), 
                'rules' => $modelFieldRules,
                'attributeNames' => $attributeNames,
                'modelTobeCheck' => $twoLayerModelName,
                'errorKey' => 'key2',
            ];
        }
        return $rules;
    }

    /*public function signup($role = Role::ROLE_USER, $status = User::STATUS_NOT_ACTIVATED, $email = null) {
        $transaction = Yii::$app->db->beginTransaction();
        $user = parent::signup($role, $status, $email);
        if($user)
        {
            if ($this->signupType == 'invite') {
                $model = UserInvite::find()->andWhere(['email' => $email])->one();
                $model->scenario = UserInvite::SCENARIO_UPDATE;
                $model->status = UserInvite::STATUS_ACTIVATED;
                $model->user_id = $user->id;
				
				if ($model->save()) {
					$this->autoFillProfile($user);
				} else {
					$transaction->rollBack();
				}
            } else {
				$this->autoFillProfile($user);
			}
			
            foreach ($this->config as $configName => $value) {
                $model = new UserConfig;
                if (is_array($value)) {

                    $arrayValue = $this->getProcessedArrayInput($value);

                    $value = json_encode($arrayValue);
                }
                $model->set($configName, $user->id, $value);
            }
        $transaction->commit();
        }
        return $user;
    }*/

    /**
     * create user.
     *
     * @return boolean if user was create.
     */

    /**
     * @return array
     */

    protected function autoFillProfile($user) {
		if (YII_DEBUG) throw new \Exception('DEPRECATED');

        $profile = $user->profile;
        $profile->attributes = $this->profile;

        $profile->save();
        $address = $profile->address;
        $address->scenario = $address::SCENARIO_NO_REQUIRED;
        $address->attributes = $this->profileAddress;
        $address->save();
    }

    //delete value that is null, and reformat array
    protected function getProcessedArrayInput($value){
        $valid = false;
        foreach ($value as $key2 => $arrayValue) {
            // prevent early version invite data no this record, so will be using non array
            if (is_array($arrayValue)) {
                foreach ($arrayValue as $key3 => $modelValue) {
                        //single row, value is null?
                    $valid = false;
                    if ($modelValue == null && !is_integer($key2)) {
                        unset($value[$key2][$key3]);
                    }
                        // check this row all column == null ?
                    elseif ($modelValue != null && is_integer($key2)) {
                        $valid = true;
                        break;
                    }
                }
                    //delete the multi column row if all column no value
                if ($valid == false && is_integer($key2)) {
                    unset($value[$key2]);
                }             
            }
        }

        if (is_integer($key2)) {
            //structure index multi column
            $value = array_values($value);
        }
        elseif($value != null) {
            //structure index single column
            $value[$key2] = array_values($value[$key2]);
        }
        return $value;
    }
}