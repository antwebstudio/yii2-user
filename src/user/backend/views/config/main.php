<?php 
use yii\helpers\Html;
use kartik\builder\Form;
use kartik\form\ActiveForm;
?>

<?= $this->render('../user/_tab', ['id' => $model->userId]) ?>

<?php $form = ActiveForm::begin(); ?>
    <?= Form::widget([
        'model' => $model,
        'form' => $form,
        //'columns' => 2,
        'attributes' => $model->getFormAttributes(),
    ]) ?>

    <div class="form-group">
        <?= Html::submitButton('Update', ['class' => 'btn btn-primary']) ?>
    </div>
<?php ActiveForm::end(); ?>