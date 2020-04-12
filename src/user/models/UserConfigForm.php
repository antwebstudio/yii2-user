<?php
namespace ant\user\models;

use Yii;
use yii\base\Model;
use yii\helpers\Html;
use yii\db\ActiveRecord;
use kartik\builder\Form;

use ant\user\models\User;
use ant\user\models\UserConfig;
use ant\rbac\Role;
use ant\interfaces\ConfigurableModelInterface;    

class UserConfigForm extends \ant\base\FormModel implements ConfigurableModelInterface
{
    //public $currentRole;
    //public $rolesChoice;

    //use for model load
    //public $models = [];

    //use for store object and model save
    public $userConfigs;
    public $userId;

    public $validConfigs;
    public $configs;
	
	public $formAttributes = [
		'currency' => [
			'type' => Form::INPUT_DROPDOWN_LIST,
			'items' => ['MYR' => 'MYR', 'USD' => 'USD', 'SGD' => 'SGD'],
			/*'rules' => [
				[['currency'], 'required'],
			],*/
			'options' => [
				'prompt' => '-- Select Currency -- '
			],
		],
		'discount_rate' => [
			'type' => Form::INPUT_TEXT,
			/*'rules' => [
				[['discount_rate'], 'required'],
				[['discount_rate'], 'number', 'min' => 0, 'max' => 100]
			],*/
			'options' => ['placeholder' => 'Discount rate , number'],
			'fieldConfig' => [
				'template' => '{label}<div class="input-group">{input}
				<span class="input-group-addon">%</span></div>{error}{hint}'
			],
		],
	];

    //public $inviteType;

    public function init() {
        foreach ((array) $this->validConfigs as $configName) {
			$configName = is_array($configName) ? $configName['attribute'] : $configName;
            $this->configs[$configName] = UserConfig::get($this->user->id, $configName);
        }
    }
	
	public function models() {
		return [
			'user:readonly' => [
				'class' => User::className(),
			],
			'configModels:array' => [
				'class' => 'ant\user\models\UserConfig',
				/*'on '.ActiveRecord::EVENT_BEFORE_VALIDATE => function($event) {
					$model = $event->sender;
				}*/
			],
		];
	}

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['configs', 'validConfigs', 'configModels'], 'safe'],
        ];
    }

    public function beforeValidate() {
		
        $models = UserConfig::find()->indexBy('config_name')->findConfigs($this->user->id);
		
        foreach ($this->configs as $name => $value) {
			if (isset($models[$name])) {
				$models[$name]->value = $value;
			} else {			
				$model = new UserConfig;
				$model->config_name = $name;
				$model->value = $value;
				$model->user_id = $this->user->id;
				$models[$name] = $model;
			}
        }
		
		/*$models = [];
		foreach ($this->configs as $name => $value) {
			$models[] = [
				'config_name' => $name,
				'value' => $value,
				'user_id' => $this->user->id,
			];
        }*/
		
		//$this->dotest();
		$this->load([$this->formName() => ['configModels' => $models]]);
		
		$tet = [];
		foreach ($this->configModels as $name => $model) {
			$tet[$model->config_name] = $model->value;
		}
		//throw new \Exception(print_r($tet,1));
		
        return true;
    }

    /*public function save($runValidation = true, $attributeNames = NULL){
        if(!$this->validate()) { 
            //store updated value to userConfigs
            foreach ((array) $this->userConfigs as $userConfig) {
                foreach ($this->models as $modelConfigName => $model) {
                    if ($modelConfigName == $userConfig->config_name) {
                        $userConfig->value = $model['value'];
                    }
                }
            }
            //restore models back to userConfigs Object so the controller actions wont fail
            $this->models = $this->userConfigs;   
            return null;
        }
        foreach ($this->models as $configName => $model) {
            if (isset($this->userConfigs) && $this->userConfigs != null) {
                foreach ($this->userConfigs as $key => $userConfig) {
                    if (!is_array($model['value'])) {
                        $value = $model['value'];
                    } else {
                        $arrayValue = $this->getProcessedArrayInput($model['value']);

                        $value = json_encode($arrayValue);
                    }
                    if ($configName == $userConfig->config_name) {
                        //update
                        $userConfig->value = $value;
                        if (!$userConfig->save()) throw new \Exception(Html::errorSummary($userConfig));
                    } else {
                        //insert
                        $newUserConfig = new UserConfig();
                        $newUserConfig->set($configName, $this->userId, $value);
                    }
                }
            } else {
                //no config before
                $value = is_array($model['value']) ? $value = json_encode($model['value']) : $model['value'];
                $newUserConfig = new UserConfig();
                UserConfig::set($this->userId, $configName, $value);
            }
        }
        if (isset($this->currentRole)) $this->changeRole($this->currentRole);
        return true;
    }*/

    public function getFormAttributes($type = null) {
        $fields = [];
        foreach ((array) $this->validConfigs as $config) {
			$attribute = is_array($config) ? $config['attribute'] : $config;
            $name = 'configs['.$attribute.']';
            $label = \yii\helpers\Inflector::camel2words($attribute, true);
			
			$options = is_array($config) ? $config : [];
			if (isset($this->formAttributes[$attribute])) {
				$fields[$name] = $this->formAttributes[$attribute];
				$fields[$name]['attribute'] = $attribute;
				if (!isset($fields[$name]['label'])) {
					$fields[$name]['label'] = $label;
				}
			} else {
				$fields[$name] = \yii\helpers\ArrayHelper::merge([
					'type' => Form::INPUT_TEXT, 
					'options' => [],
					'attribute' => $attribute,
					'label' => $label,
				], $options);
			}
        }
        return $fields;
    }

}