<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

$this->context->layout = '/small';
$this->title = 'Activate Account';
?>
<h2><?= $this->title ?></h2>

<?php $form = ActiveForm::begin() ?>
    <?= $form->errorSummary($model, ['class' => 'alert-danger alert']) ?>

    <?= $form->field($model, 'password')->textInput(['type' => 'password']) ?>
    <?= $form->field($model, 'confirmPassword')->textInput(['type' => 'password']) ?>

    <?= Html::submitButton('Activate', ['class' => 'btn btn-primary']) ?>
<?php ActiveForm::end() ?>