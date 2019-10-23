<?php  
namespace ant\user\models;

use Yii;
use yii\db\ActiveRecord;
use ant\user\models\InviteRequest;

class UserConfig extends ActiveRecord{

    protected $_fields;
    public $fieldName;
    public $inviteType;

	public function init(){
		parent::init();
	}

    public static function tableName()
    {
        return '{{%user_config}}';
    }

    public static function find() {
        return new \ant\user\models\query\UserConfigQuery(get_called_class());
    }

    public static function set($userId, $configName, $value) {
		$config = self::find()->andWhere(['user_id' => $userId, 'config_name' => $configName])->one();
		
		if (!isset($config)) {
			$config = new UserConfig([
				'user_id' => $userId,
				'config_name' => $configName,
			]);
		}
        $config->value = $value;
		if (!$config->save()) throw new \Exception('Failed to set user config. (UserID: '.$userId.', Config: '.$configName.') ');
		
		return $config;
    }
	
	public static function get($userId, $configName) {
		$config = self::find()->andWhere(['user_id' => $userId, 'config_name' => $configName])->one();
		
		if (isset($config)) {
			return $config->value;
		}
	}

    // public function getFields(){
    //     if ($this->_fields != null ) {
    //         return $this->_fields !== [] ? $this->_fields : null;
    //     }
    //     $userInvite = Yii::createObject(Yii::$app->getModule('user')->inviteModel[$this->inviteType]['model']);
    //     //$userInvite = new InviteRequest;
    //     //$userInvite->setConfigInviteSetting($this->inviteType);

    //     // echo "<pre>";
    //     // print_r($userInvite);
    //     // echo "</pre>";
    //     // die;

    //     $this->_fields = $userInvite->fields;
    //     return $this->_fields !== [] ? $this->_fields : null;
    // }

    // public function getField(){
    //     $fields = $this->fields;

    //     return $fields[$this->fieldName];
    // }

    // public function getFieldInputType(){
    //     $field = $this->getField();
    //     return isset($field['type']) ? $field['type'] : 'textInput';
    // }

    // public function getFieldItems(){
    //     $field = $this->getField();
    //     return isset($field['items']) ? $field['items'] : [];
    // }

    // public function getFieldLabel(){
    //     return ucwords(str_replace('_', ' ', $this->fieldName));
    // }

    // public function getInputOption(){
    //     $field = $this->getfield();
    //     return isset($field['inputOption']) ? $field['inputOption'] : [];
    // }

    // public function getFieldOption(){
    //     $field = $this->getField();
    //     return isset($field['fieldOption']) ? $field['fieldOption'] : [];
    // }

    public function rules()
    {
        return
        [
            [['config_name' , 'value'], 'string'],
            [['user_id'], 'integer'],
            [['user_id', 'config_name'], 'unique', 'targetAttribute' => ['user_id', 'config_name']]
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'config_name' => 'Config Name',
        ];
    } 

}
?>