<?php
use ant\widgets\ActiveForm;
use yii\helpers\Html;

use ant\user\models\UserProfile;
use ant\widgets\SubmitButton\SubmitButton;
use ant\widgets\Alert;
use ant\address\widgets\Address\Address;

$action = $model->getModel('profile')->isNewRecord ? 'Create' : 'Edit';

$this->title = $action . " Profile";

$this->params['title'] = $this->title;

$formId = 'form-user-profile';

$this->params['headerRightPanel'][] = SubmitButton::widget([
    'label' => $action, 
    'form' => $formId,
    'options' => [
        'class' => 'btn btn-primary',
    ],
]);

$this->params['headerRightPanel'][] = Html::a('Go Back', $url = ['/user/profiles/index'], ['class' => 'btn btn-default']);


$this->params['breadcrumbs'][] = ['label' => 'Setting', 'url' => ['/user/setting']];
$this->params['breadcrumbs'][] = ['label' => 'My Profiles', 'url' => ['/user/profiles/index']];
$this->params['breadcrumbs'][] = $action = $model->getModel('profile')->isNewRecord ? $this->title : $action . ':' . $model->getModel('profile')->fullName;

$this->params['sideNav'] = $sideNav;
?>
<div class="page-user-profile-create">
    <?=Alert::widget(); ?>
    
	<?php $form = ActiveForm::begin(['id' => $formId]); ?>
        <div class="form-panel">
            <h2 class="heading">Profile</h2>
            <?=$form->field($model->getModel('profile'), 'firstname')->textInput(); ?>
            
            <?=$form->field($model->getModel('profile'), 'lastname')->textInput(); ?>

            <?= $form->field($model->getModel('profile'), 'company')->textInput() ?>

            <?= $form->field($model->getModel('profile'), 'gender')->dropDownList([
                0 => 'Select ...',
                UserProfile::GENDER_MALE => 'Male',
                UserProfile::GENDER_FEMALE => 'Female',
            ], ['id' => 'gender']); ?>
        </div>

        <div class="form-panel">
            <h2 class="heading">Contact Information</h2>
            
            <?=$form->field($model->getModel('profile'), 'email')->textInput(); ?>
            
            <?=$form->field($model->getModel('profile'), 'contact')->textInput(); ?>

            <?=Address::widget([
                'model' => $model->getModel('address'),
                'form' => $form,
            ]); ?>
        </div>

	<?php ActiveForm::end(); ?>
</div>