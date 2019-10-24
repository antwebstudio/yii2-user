<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \ant\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Profile Setting';

$this->params['page-header']['title'] = $this->title;
$this->params['page-header']['breadcrumbs'][] = $this->title;
?>
<div class="page-user-default-profile container">
	<div class="row">
        <div class="col-lg-5">
        	<?php $form = ActiveForm::begin(['id' => 'form-profile']); ?>


                <?= $form->field($model->getModel('user'), 'currentPassword')->passwordInput() ?>
                <?= $form->field($model->getModel('user'), 'password')->passwordInput() ?>
                <?= $form->field($model->getModel('user'), 'confirmPassword')->passwordInput() ?>


            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>