<?php
/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\PasswordResetRequestForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = Yii::t('user', 'Forgot Password');
$this->params['title'] = $this->title;

$this->params['page-header']['title'] = $this->title;
$this->params['page-header']['breadcrumbs'][] = $this->title;

$this->context->layout = '/small';
?>
<div class="page-user-signin-request-password-reset">
	<p><?= Yii::t('user', 'Enter your email address to request a password reset.') ?></p>
	<!--  <p class="description">Please fill out your email. A link to reset password will be sent there.</p> -->

	<?php $form = ActiveForm::begin(['id' => 'request-password-reset-form']); ?>

		<?= $form->field($model, 'email')->textInput(['autofocus' => true]) ?>

		<div class="form-group submit">
			<?= Html::submitButton(Yii::t('user', 'Send'), ['class' => 'btn btn-primary']) ?>
		</div>
	<?php ActiveForm::end() ?>
</div>
