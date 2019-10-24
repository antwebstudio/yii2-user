<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\PasswordResetRequestForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Request invite form';

$this->params['page-header']['title'] = $this->title;
$this->params['page-header']['breadcrumbs'][] = $this->title;
?>
<div class="page-user-signin-request-password-reset container">
    

    <p class="description"><h4>Please fill out the email. A link to send invite will be sent there.</h4></p>

    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'request-invite-form']); ?>

                <?= $form->field($model, 'email')->textInput(['autofocus' => true]) ?>

                <div class="form-group">
                    <?= Html::submitButton('Send', ['class' => 'btn btn-primary']) ?>
                </div>

            <?php ActiveForm::end(); ?>
        </div>
    
        <div class="col-lg-5">
             <h4 class="heading">
                Problem ?
                <p>Don't have receive email link? Please fill out correct email to resend the email link.</p>
            </h4>

             <?php $form = ActiveForm::begin(['id' => 'resend-form']); ?>


                <div class="form-group">
                    <?= Html::submitButton('Resend Code', ['class' => 'btn btn-primary', 'name' => 'submit-button', 'value' => 'resend']) ?>
                </div>

            <?php ActiveForm::end(); ?>
        </div>

    </div>
</div>