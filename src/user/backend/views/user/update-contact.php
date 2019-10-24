<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;

$this->title = 'Address';
?>
<?= $this->render('_tab', ['id' => $userId]) ?>

<?php //if (Yii::$app->getModule('user')->profileAddressFields): ?>
	<div class="form-panel.no-padding">

		<?php $form = ActiveForm::begin() ?>
		
			<?= $form->errorSummary($model, ['class' => 'alert alert-danger']) ?>
			
			<?= \ant\address\widgets\Address\Address::widget([
				'form' => $form,
				'mode' => 'advance',
				'model' => $model,
			]) ?>

			<div class="form-group">
				<?= Html::submitButton('Save', ['class' => 'btn btn-primary', 'name' => 'update-profile-button']) ?>
			</div>
			
		<?php ActiveForm::end() ?>
	</div>
<?php //endif ?>