<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $modelActivationForm \ant\user\models\ActivationForm */
/* @var $modelActivationCodeRequestForm \ant\user\models\ActivationCodeRequestForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

$this->title = 'Change E-mail Success';

$this->params['title'] = $this->title;
$this->params['breadcrumbs'][] = ['label' => 'Setting', 'url' => ['/user/setting']];
$this->params['breadcrumbs'][] = $this->title;

$this->params['sideNav'] = $sideNav;
?>
<div class="page-user-setting-email-change-success text-center">
	<span class="fa-stack fa-lg fa-5x">
		<i class="fa fa-circle fa-stack-2x"></i>
		<i class="fa fa-check fa-stack-1x fa-inverse"></i>
	</span>

	<h2 class="heading">
		Congratulations!

		<p class="text-center">Your email has change successfully.</p>
	</h2>
</div>
