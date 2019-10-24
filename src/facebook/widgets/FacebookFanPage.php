<?php
namespace ant\facebook\widgets;

use Yii;
use yii\helpers\Url;

class FacebookFanPage extends \yii\base\Widget {
	public $url;
	
	public function init() {
	}
	
	public function run() {
		if (isset($this->url) && trim($this->url) != '') {
			return $this->render('facebook-fan-page', []);
		}
	}
}