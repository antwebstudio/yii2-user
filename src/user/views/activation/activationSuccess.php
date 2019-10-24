<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $modelActivationForm \ant\user\models\ActivationForm */
/* @var $modelActivationCodeRequestForm \ant\user\models\ActivationCodeRequestForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

$this->title = 'Account Activated';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="page-user-signin-activate-success">
    <div class="row">
        <div class="col-lg-12 text-center">
        	<div>
        		<span class="fas fa-stack fa-lg fa-5x">
					<i class="fas fa fa-circle fa-stack-2x"></i>
					<i class="fas fa fa-check fa-stack-1x fa-inverse"></i>
				</span>

				<h2 class="heading">
					Congratulations!

					<p class="text-center">Your account is now activated</p>
				</h2>
        	</div>
        </div>
    </div>
</div>
