<?php 
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use unclead\multipleinput\MultipleInput;

$this->title = 'Update User';
$this->params['breadcrumbs'][] = $this->title;

$textarea = [
	'speciality', 'affiliation', 'interest',
];

$fields = [
	'origin' => 'Place of Origin 來自哪裡',
	'city' => 'Current City 長居城市',
	'language' => 'Languages 擅長及其它可溝通的語言',
	'speciality' => 'Strength / Specialities / Occupation 专长 / 专研领域 / 职业',
	'affiliation' => 'Affiliation 所屬機構/社團',
	'interest' => 'Interest 興趣',
];
?>
<?= $this->render('_tab', ['id' => $userId]) ?>

<?php $form = ActiveForm::begin() ?>
	<?= $form->errorSummary($model, ['class' => 'alert alert-danger']) ?>
	
	<?php foreach ($fields as $name => $label): ?>
		<?php if (in_array($name, $textarea)): ?>
			<?= $form->field($model, 'data['.$name.']')->label($label)->textArea() ?>
		<?php else: ?>
			<?= $form->field($model, 'data['.$name.']')->label($label) ?>
		<?php endif ?>
	<?php endforeach ?>
  
	<div class="form-group">
		<?= Html::submitButton('Update', ['class' => 'btn btn-primary']) ?>
	</div>
<?php ActiveForm::end(); ?>