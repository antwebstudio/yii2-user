<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\PasswordResetRequestForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Change Email';

$this->params['title'] = $this->title;
$this->params['breadcrumbs'][] = ['label' => 'Setting', 'url' => ['/user/setting']];
$this->params['breadcrumbs'][] = $this->title;

$this->params['sideNav'] = $sideNav;
?>
<div class="page-user-setting-request-email-change">
    
    <p class="description">
        Current email : <?=Yii::$app->user->identity->email; ?>
    </p>
    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'request-password-reset-form']); ?>

                <?= $form->field($model, 'email')->textInput(['autofocus' => true]) ?>

                <div class="form-group">
                    <?= Html::submitButton('Change Email', ['class' => 'btn btn-primary']) ?>
                </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>
