<?php 
use yii\helpers\Html;
use ant\user\models\UserInvite;
use kartik\form\ActiveForm;
use unclead\multipleinput\MultipleInput;

$isNewRecord = $model->getModel('userInvite')->isNewRecord;

$this->title = $isNewRecord ? 'Send Invite' : 'Update invite user ID ' . $model->getModel('userInvite')->id;

$this->params['breadcrumbs'][] = $this->title;
$this->params['content-header-buttons'][] = Html::a('<span class="btn-label"><i class="fa fa-arrow-left"></i></span> Return Invite List', ['index'], ['class' => 'btn btn-sm btn-primary btn-labeled']);

$action = $isNewRecord ? ['send'] : ['update', 'id' => $model->getModel('userInvite')->id ];
?>

<div class="page-user-signin-request-password-reset">
	<?php $form = ActiveForm::begin([
		'fieldClass' => 'ant\widgets\ActiveField',
		'action' => $action, 
		'id' => 'request-invite-form', 
		'method' => 'post',
	 ]) ?>
		<?= $form->errorSummary($model->getModels(), ['class' => 'alert alert-danger']) ?>
		
		<?php if ($isNewRecord): ?>
			<p class="description">
				<h4>Please fill out the email. A link to send invite will be sent there.</h4>
			</p>
		<?php endif ?>
			 <?php if ($isNewRecord): ?>
				<?= $form->field($model->getModel('userInvite'), 'email', [
					'template' => '<div class="input-group"><span class="input-group-addon"><i class="fa fa-envelope-o fa-fw"></i></span>{input}</div>'
				])->textInput(['autofocus' => true, 'class' => 'form-control', 'placeholder' => 'Email Address']) ?>

				<?= $form->field($model->getModel('userInvite'), 'type')->hiddenInput(['value' => \ant\user\backend\Module::INVITE_TYPE_ROLE])->label(false) ?>
			<?php else: ?>
				<?= $form->field($model->getModel('userInvite'), 'email')->hiddenInput()->label(false) ?>
			<?php endif ?>

			<?= $form->field($model->getModel('userInvite'), 'role')->dropDownList($model->getAvailableRoles(), ['prompt' => 'Select a role']) ?>

			<?php foreach ($model->getCustomFormConfigs($form) as $formConfig): ?>
				<?= \kartik\builder\Form::widget($formConfig) ?>
			<?php endforeach ?>

			<div class="form-group">
				<?= Html::submitButton($isNewRecord ? 'Send' : 'Update', ['class' => 'btn btn-primary']) ?>
			</div>

	<?php ActiveForm::end(); ?>

</div>