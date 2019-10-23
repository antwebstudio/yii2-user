<?php
namespace ant\rbac;

class AccessRule extends \yii\filters\AccessRule {
	public $modules;
	
	public function allows($action, $user, $request) {
        if ($this->matchAction($action)
            && $this->matchRole($user)
            && $this->matchIP($request->getUserIP())
            && $this->matchVerb($request->getMethod())
            && $this->matchController($action->controller)
			&& $this->matchModule($action->controller->module)
            && $this->matchCustom($action)
        ) {
            return $this->allow ? true : false;
        }

        return null;
    }
	
	protected function matchModule($module) {
		return empty($this->modules) || in_array($module->id, $this->modules, true);
	}
}