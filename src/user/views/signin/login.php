<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \ant\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->context->layout = '/small';

$this->title = 'Login';
$this->params['title'] = $this->title;

$this->params['page-header']['title'] = $this->title;
$this->params['page-header']['breadcrumbs'][] = $this->title;
?>
<div class="page-user-signin-login">
	<p class="description">Please fill out the following fields to login.</p>

	<?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

		<?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>

		<?= $form->field($model, 'password')->passwordInput() ?>
		<div class="row">
			<div class="col-6">
				<div style="margin:1em 0;">
				<?= $form->field($model, 'rememberMe')->checkbox() ?>
				</div>
			</div>
			<div class="col-6" style="text-align:right; text-decoration:underline;">
				<div style="color:red;margin:1em 0;">
					<?= Html::a('Forgot Password?', ['/user/signin/request-password-reset']) ?>
				</div>
			</div>
		</div>

		<div class="form-group submit">
			<?= Html::submitButton('Login', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
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
			<?php \yii\authclient\widgets\AuthChoice::end(); ?>
		<?php endif ?>

	<?php ActiveForm::end(); ?>
</div>