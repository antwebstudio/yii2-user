<?php
namespace ant\facebook\widgets;

use Yii;
use yii\helpers\Url;

class FacebookPixel extends \yii\base\Widget {
	public $pixelId;
	
	public function init() {
	}
	
	public function run() {
		if (isset($this->pixelId) && trim($this->pixelId) != '') {
			return $this->render('facebook-pixel', []);
		}
	}
}