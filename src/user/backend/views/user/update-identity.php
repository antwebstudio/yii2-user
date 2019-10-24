<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;

$this->title = 'Identity Information';
?>
<?= $this->render('_tab', ['id' => $userId]) ?>

<?php //if (Yii::$app->getModule('user')->profileAddressFields): ?>
	<div class="form-panel.no-padding">

		<?php $form = ActiveForm::begin(); ?>
		
			<?= $form->errorSummary($model, ['class' => 'alert alert-danger']) ?>
			
			<?= $form->field($model, 'identitiesValue')->widget(\unclead\multipleinput\MultipleInput::className(), [
				'addButtonPosition' => \unclead\multipleinput\MultipleInput::POS_FOOTER,
				'allowEmptyList'    => true,
				'sortable'         => true,
				'columns' => [
					[
						'name' => 'id',
						'type' => \unclead\multipleinput\components\BaseColumn::TYPE_HIDDEN_INPUT,
						'defaultValue' => 0
					],
					[
						'name' => 'type',
						'type' => \unclead\multipleinput\components\BaseColumn::TYPE_DROPDOWN,
						'items' => $model->types,
					],
					[
						'name' => 'value',
						'type' => \unclead\multipleinput\components\BaseColumn::TYPE_TEXT_INPUT,
					],
				]
			]) ?>

			<div class="form-group">
				<?= Html::submitButton('Save', ['class' => 'btn btn-primary', 'name' => 'update-profile-button']) ?>
			</div>
			
		<?php ActiveForm::end() ?>
	</div>
<?php //endif ?>