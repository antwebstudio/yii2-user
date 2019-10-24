<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $activationForm \ant\user\models\ActivationForm */
/* @var $updateEmailForm \ant\user\models\ActivationCodeupdateEmailForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

$this->title = 'Account Activation';

$this->params['page-header']['title'] = $this->title;
$this->params['page-header']['breadcrumbs'][] = $this->title;

$updateEmailId = 'update-email';
$updateEmailButtonId = 'update-email-btn';
?>
<div class="page-user-signin-activate">    
    <div class="row">
        <div class="col-md-6">
            <h2 class="heading">Activate Account</h2>
			<p>To activate your acconunt, please fill in the <?= $activationForm->expectedCodeLength ?> digit activation code sent to your email.</p>
            
            <?php $form = ActiveForm::begin() ?>

                <?= $form->field($activationForm, 'activationCode')->textInput(['autofocus' => true]) ?>

                <div class="form-group">
                    <?= Html::submitButton('Activate Account', ['class' => 'btn btn-primary']) ?>
                </div>

            <?php ActiveForm::end() ?>
        </div>

        <div class="col-md-6">
			<h2 class="heading">Problem ?</h2>
			<p>Don't receive activation code? Try to change email address associated with this account.</p>
					
			<?= Html::a('Resend Code', ['resend-code'], ['data-method' => 'post', 'class' => 'mb-3 btn btn-secondary']) ?>
			
			<?php $collapse = \ant\widgets\Collapse::begin([
				'show' => $updateEmailForm->hasErrors(),
				'autoToggleButton' => true,
				'toggleButton' => [
					'label' => 'Change Email',
					'options' => ['class' => 'mb-3 btn btn-secondary'],
				],
			]) ?>
				<h4>Change Email</h4>
				<?php $form = ActiveForm::begin() ?>
					<?= $form->field($updateEmailForm, 'email')->textInput() ?>

					<div class="form-group">
						<?= Html::submitButton('Update Email', ['class' => 'btn btn-primary']) ?>
						<a href="#" data-toggle="collapse" class="btn btn-secondary">Cancel</a>
					</div>
				<?php ActiveForm::end() ?>
			<?php $collapse = \ant\widgets\Collapse::end() ?>
        </div>
    </div>
</div>