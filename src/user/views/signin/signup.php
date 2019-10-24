<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */

use yii\helpers\Html;
//use yii\bootstrap\ActiveForm;
use kartik\form\ActiveForm;
use kartik\builder\Form;
use unclead\multipleinput\MultipleInput;
use kartik\builder\FormGrid;

$this->context->layout = '/small';

$this->title = 'Signup';
$this->params['title'] = $this->title;

$this->params['page-header']['title'] = $this->title;
$this->params['page-header']['breadcrumbs'][] = $this->title;
?>
<div class="page-user-signin-signup">
	<!--<div class="user-signup"><i class="fa fa-7x fa-user-plus"></i></div>-->
	<!-- <p class="signUpTitle">REGISTER</p>-->
	<p class="description">Please fill out the following fields to signup.</p>

	<?php $form = ActiveForm::begin() ?>

		<?= $form->errorSummary($model->getModels(), ['class' => 'alert alert-danger']) ?>

		<?= $form->field($model, 'username') ?>
		<?= $form->field($model->getModel('user'), 'email') ?>
		<?= $form->field($model, 'password')->passwordInput() ?>
		<?= $form->field($model, 'confirmPassword')->passwordInput() ?>

		<div class="form-group submit">
			<?= Html::submitButton('Sign Up', ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
		</div>

		<?php if (class_exists('yii\authclient\widgets\AuthChoice') && isset(Yii::$app->authClientCollection)): ?>
			<div class="divider">OR</div>

			<?php $authAuthChoice = \yii\authclient\widgets\AuthChoice::begin([
				'baseAuthUrl' => ['/user/auth-client/auth'],
				'popupMode' => false,
			]); ?>
				<?php foreach ($authAuthChoice->getClients() as $client): ?>
					<?= Html::a('<i class="fa fw fa-'.$client->name.'"></i> '.Yii::t('authclient', 'Connect with {client}', ['client' => $client->title]), $authAuthChoice->createClientUrl($client), ['class' => 'col-sm-12 btn btn-primary']) ?>
				<?php endforeach; ?>
			<?php \yii\authclient\widgets\AuthChoice::end() ?>
		<?php endif ?>

	<?php ActiveForm::end() ?>
</div>
