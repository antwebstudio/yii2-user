<?php  
namespace ant\user\components;

use Yii;
use yii\base\Component;
use yii\helpers\Html;
use ant\user\models\UserConfig as Config;
use ant\user\models\User;
use ant\rbac\Role;

Class UserConfig extends Component{

    protected $_configs;

    public function init(){
        parent::init();
    }
	
	public function clearCache() {
		$this->_configs = null;
	}

    public function get($configName, $defaultValue = null){
		$this->ensureConfigsLoaded();
		
		if (is_array($configName)) {
			return $this->getArray($configName);
		}
		
		if (isset($this->_configs[$configName])) {
			return $this->_configs[$configName]->value;
		}
        return $defaultValue;
    }

    public function set($configName, $value = null){
		$this->ensureConfigsLoaded();
		
		if (is_array($configName)) {
			return $this->setArray($configName);
		}
		
		if (isset($this->_configs[$configName])) {
			$this->_configs[$configName]->value = $value;
			
			if (!$this->_configs[$configName]->save()) throw new \Exception('Failed to save user config. '.Html::errorSummary($newConfig));
		} else {
			$newConfig = new Config;
			$newConfig->config_name = $configName;
			$newConfig->user_id = Yii::$app->user->id;
			$newConfig->value = $value;
			
			if (!$newConfig->save()) throw new \Exception('Failed to save user config. '.Html::errorSummary($newConfig));
			
			$this->_configs[$configName] = $newConfig;
		}
		return $this->_configs[$configName];
    }
	
	protected function ensureConfigsLoaded() {
        if ($this->_configs == null) {
            $this->_configs = Config::find()->andWhere(['user_id' => Yii::$app->user->id])->indexBy('config_name')->all();
        }
	}
	
	protected function getArray($configNameArray) {
		$return = [];
		foreach ($configNameArray as $name) {
			$return[$name] = $this->get($name);
		}
		return $return;
	}
	
	protected function setArray($configNameValuePair) {
		foreach ($configNameValuePair as $name => $value) {
			$this->set($name, $value);
		}
	}

}
?>