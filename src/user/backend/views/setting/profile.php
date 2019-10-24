<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \backend\models\SignupForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

use trntv\filekit\widget\Upload;
use kartik\file\FileInput;

use ant\user\models\UserProfile;
use ant\address\widgets\Address\Address;

$this->title = "Update Profile";

$this->params['title'] = $this->title;

$this->params['breadcrumbs'][] = ['label' => 'Setting', 'url' => ['/user/setting']];
$this->params['breadcrumbs'][] = $this->title;

$this->params['sideNav'] = $sideNav;
?>
<div class="page-user-setting-profile">
    <?php $form = ActiveForm::begin(['id' => 'form-update-profile']); ?>

        <div class="form-panel">
            <h2 class="heading heading-underline">Profile</h2>

            <?= $form->field($model->getModel('profile'), 'picture')->widget(
                Upload::classname(),
                [
                    'url' => ['avatar-upload']
                ]
            )?>

            <?= $form->field($model->getModel('profile'), 'firstname')->textInput() ?>
            
            <?= $form->field($model->getModel('profile'), 'lastname')->textInput() ?>

            <?= $form->field($model->getModel('profile'), 'company')->textInput(['disabled' => true]) ?>

            <?= $form->field($model->getModel('profile'), 'gender')->dropDownList([
                0 => 'Select ...',
                UserProfile::GENDER_MALE => 'Male',
                UserProfile::GENDER_FEMALE => 'Female',
            ], ['id' => 'gender']); ?>
        </div>
    

        <div class="form-panel">
            <h2 class="heading heading-underline">Contact Information</h2>
            
            <?= $form->field($model->getModel('profile'), 'contact')->textInput() ?>
                
            
            <?=Address::widget([
                'form' => $form,
                'model' => $model->getModel('address'),
            ]); ?>
        </div>

        
        <div class="form-group">
            <?= Html::submitButton('Update Profile', ['class' => 'btn btn-primary', 'name' => 'update-profile-button']) ?>
        </div>

    <?php ActiveForm::end(); ?>
</div>
