<?php
use yii\widgets\ActiveForm;
use yii\helpers\Html;
?>
<style>
    .label-row { display: block; }
</style>
<?= $this->render('_tab', ['id' => $userId]) ?>

<?php $form = ActiveForm::begin() ?>

    <?php /*
    <?= $form->field($model, 'roles')->widget('ant\widgets\MultipleChoice', [
        'data' => $model->getAvailableRoles(),
        'options' => [
            'class' => 'clearfix',
            'item' => function($index, $label, $name, $checked, $value) use ($model) {
                return Html::checkbox($name, $checked, [
                    'label' => ucfirst($label),
                    'value' => $value,
                    'labelOptions' => ['class' => 'label-row'],
                    'disabled' => $model->isOptionDisabled($value),
                ]);
            }
        ],
    ]) ?>
    */?>
    
    <?= $form->field($model, 'roles')->checkboxList($model->getAvailableRoles(), [
        'item' => function($index, $label, $name, $checked, $value) use ($model) {
            return Html::checkbox($name, $checked, [
                'label' => ucfirst($label),
                'value' => $value,
                'labelOptions' => ['class' => 'label-row'],
                'disabled' => $model->isOptionDisabled($value),
            ]);
        }
    ]) ?>

    <?= Html::submitButton('Save', ['class' => 'btn btn-primary']) ?>

<?php ActiveForm::end() ?>