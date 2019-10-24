<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \backend\models\SignupForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Change Password';

$this->params['title'] = $this->title;
$this->params['breadcrumbs'][] = ['label' => 'Setting', 'url' => ['/user/setting']];
$this->params['breadcrumbs'][] = $this->title;

$this->params['sideNav'] = $sideNav;
?>

<?= $this->render('_nav') ?>

<div class="page-user-setting-password">
    <?php $form = ActiveForm::begin(['id' => 'form-change-password']); ?>

        <?= $form->field($model, 'oldPassword')->passwordInput(['autofocus' => true]) ?>

        <?= $form->field($model, 'password')->passwordInput() ?>

        <?= $form->field($model, 'confirmPassword')->passwordInput() ?>

        <div class="form-group">
            <?= Html::submitButton('Change Password', ['class' => 'btn btn-primary', 'name' => 'change-password-button']) ?>
        </div>

    <?php ActiveForm::end(); ?>
</div>
