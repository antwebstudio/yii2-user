<?php
if (YII_DEBUG) throw new \Exception('DEPRECATED'); /

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $modelActivationForm \ant\user\models\ActivationForm */
/* @var $modelActivationCodeRequestForm \ant\user\models\ActivationCodeRequestForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

use ant\user\models\ActivationCodeRequestForm;

$this->title = 'Account Activation';

$this->params['page-header']['title'] = $this->title;
$this->params['page-header']['breadcrumbs'][] = $this->title;
?>
<div class="page-user-signin-activate">    
    <div class="row">
        <div class="col-md-6">
            <h2 class="heading">
                Activate Account
                <p>To activate your acconunt, please fill out the <?=ActivationCodeRequestForm::ACTIVATION_CODE_LENGTH;?> digit activation code to activate account.</p>
            </h2>

            <?php $form = ActiveForm::begin(['id' => 'activate-form']); ?>

                <?= $form->field($modelActivationForm, 'activationCode')->textInput(['autofocus' => true]) ?>

                <div class="form-group">
                    <?= Html::submitButton('Activate Account', ['class' => 'btn btn-primary', 'name' => 'submit-button', 'value' => 'activate']) ?>
                </div>

            <?php ActiveForm::end(); ?>
        </div>

        <div class="col-md-6">
             <h2 class="heading">
                Problem ?
                <p>Don't have receive activation code? Please fill out correct email to resend the activation code.</p>
            </h2>

             <?php $form = ActiveForm::begin(['id' => 'resend-form']); ?>

                <?= $form->field($modelActivationCodeRequestForm, 'email')->textInput() ?>

                <div class="form-group">
                    <?= Html::submitButton('Resend Code', ['class' => 'btn btn-primary', 'name' => 'submit-button', 'value' => 'resend']) ?>
                </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>