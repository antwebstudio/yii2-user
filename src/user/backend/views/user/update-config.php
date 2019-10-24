<?php 
use yii\helpers\Html;
use yii\bootstrap\Nav;
use kartik\builder\Form;
use kartik\builder\FormGrid;
use kartik\form\ActiveForm;
use unclead\multipleinput\MultipleInput;

$this->title = 'Update User';
$this->params['breadcrumbs'][] = $this->title;

//throw new \Exception(print_r($formModel->getFormAttributes($type),1));
?>
<?= $this->render('_tab', ['id' => $formModel->user->id]) ?>

<?php $form = ActiveForm::begin([
	'action' => ['', 'id' => $formModel->user->id, 'type' => $type ], 
	'id' => 'request-invite-form'
]) ?>
    <?= $form->errorSummary($formModel, ['class' => 'alert alert-danger']) ?>
	
	<?= Form::widget([
		'form'=> $form,
		'model' => $formModel,
		'attributes' => $formModel->getFormAttributes($type),
	]) ?>
	
	<div class="form-group">
		<?= Html::submitButton('Update', ['class' => 'btn btn-primary']) ?>
	</div>
<?php ActiveForm::end() ?>