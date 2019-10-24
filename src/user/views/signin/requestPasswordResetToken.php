<?php
if (YII_DEBUG) throw new \Exception('DEPRECATED'); // Added on 04-10-2019

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\PasswordResetRequestForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Forgot Password';
$this->params['title'] = $this->title;

$this->params['page-header']['title'] = $this->title;
$this->params['page-header']['breadcrumbs'][] = $this->title;

$this->context->layout = '/small';
?>
<div class="page-user-signin-request-password-reset">
	<p>Enter your email address to request a password reset.</p>
	<!--  <p class="description">Please fill out your email. A link to reset password will be sent there.</p> -->

	<?php $form = ActiveForm::begin(['id' => 'request-password-reset-form']); ?>

		<?= $form->field($model, 'email')->textInput(['autofocus' => true]) ?>

		<div class="form-group submit">
			<?= Html::submitButton('Send', ['class' => 'btn btn-primary']) ?>
		</div>
	<?php ActiveForm::end() ?>
</div>
