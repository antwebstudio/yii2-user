<?php
use yii\helpers\Html;
use kartik\builder\Form;
use kartik\builder\FormGrid;
use kartik\form\ActiveForm;

$this->title = 'Create New User';
?>


<?php $form = ActiveForm::begin([
	'fieldClass' => 'ant\widgets\ActiveField',
]) ?>
	
	<?php if ($model instanceof \ant\base\FormModel): ?>
	
		<?= $form->errorSummary($model->getModels(), ['class' => 'alert alert-danger']) ?>
		
		<?= Form::widget([
			'form'=> $form,
			'model' => $model->getModel('user'),
			'attributes' => $model->getFormAttributes('user'),
		]) ?>
		
		<?= Form::widget([
			'form'=> $form,
			'model' => $model->getModel('identity'),
			'attributes' => $model->getFormAttributes('identity'),
		]) ?>
		
		<?= Form::widget([
			'form'=> $form,
			'model' => $model->getModel('profile'),
			'attributes' => $model->getFormAttributes('profile'),
		]) ?>
		
	<?php else: ?>

		<?= $form->errorSummary($model, ['class' => 'alert alert-danger']) ?>

		<?= $form->field($model, 'username') ?>
		<?= $form->field($model, 'email') ?>
		<?= $form->field($model, 'password')->passwordInput() ?>
		<?= $form->field($model, 'confirmPassword')->passwordInput() ?>
		
	<?php endif ?>
		
	<?= Html::submitButton('Create', ['class' => 'btn btn-primary']) ?>
	
<?php ActiveForm::end() ?>