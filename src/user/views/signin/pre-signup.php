<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */

use yii\helpers\Html;
//use yii\bootstrap\ActiveForm;
use kartik\form\ActiveForm;
use kartik\builder\Form;
use unclead\multipleinput\MultipleInput;
use kartik\builder\FormGrid;

$this->title = 'Choose Signup Type';

$this->params['page-header']['title'] = $this->title;
$this->params['page-header']['breadcrumbs'][] = $this->title;

$signupTypes = [];
?>
<div class="page-user-signin-signup container">
    <div class="row">
		<?php foreach ($signupTypes as $key => $signupType): ?>
			<div class="col-md-6">
				<?= Html::a(ucfirst($signupType), ['/user/signin/signup', 'signupType' => $signupType], ['class' => 'btn btn-primary']) ?>
			</div>
		<?php endforeach ?>
    </div>
</div>
