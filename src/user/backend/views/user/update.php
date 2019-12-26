<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;


use ant\file\widgets\Upload;
use kartik\depdrop\DepDrop;
use kartik\file\FileInput;
use kartik\select2\Select2;

use ant\user\models\UserProfile;
use ant\address\widgets\Address\Address;
use ant\address\models\AddressCountry;
use ant\address\models\AddressZone;
use kartik\builder\Form;
use kartik\builder\FormGrid;
use kartik\form\ActiveForm;
$this->title = "Update Profile";

$this->params['title'] = $this->title;

$this->params['breadcrumbs'][] = ['label' => 'Setting', 'url' => ['/user/setting']];
$this->params['breadcrumbs'][] = $this->title;

$this->params['sideNav'] = $sideNav;

//varaible for foreach use
$modelType = 'default';
$userUsing = 'admin';
?>

<?= $this->render('_tab', ['id' => $userId]) ?>

<div class="page-user-setting-profile">
    <?php $form = ActiveForm::begin(['id' => 'form-update-profile']); ?>
        <div class="form-panel">
            <h2 class="heading heading-underline">Profile</h2>
			
			<?= $form->errorSummary($model->getModels(), ['class' => 'alert alert-danger']) ?>
			
			<?= Form::widget([
				'model' => $model->getModel('profile'),
				'form' => $form,
				'attributes' => $model->getFormAttributes('profile'),
			]) ?>
			
			<?php /*
            <?php
            if (isset(Yii::$app->getModule('user')->profileFields[$modelType])
                && isset(Yii::$app->getModule('user')->profileFields[$modelType][$userUsing]) 
                && (isset(Yii::$app->getModule('user')->profileFields[$modelType][$userUsing][$editingUser]) || isset(Yii::$app->getModule('user')->profileFields[$modelType][$userUsing]['user']) )
                && (isset(Yii::$app->getModule('user')->profileFields[$modelType][$userUsing][$editingUser]['fields']) || isset(Yii::$app->getModule('user')->profileFields[$modelType][$userUsing]['user']['fields']) )
            ) {
                if (isset(Yii::$app->getModule('user')->profileFields[$modelType][$userUsing]['user']['fields'])) {
                    $editingUser = 'user';
                }
                $fieldsArray = Yii::$app->getModule('user')->profileFields;
            } else {
                $fieldsArray = Yii::$app->getModule('user')->getDefaultProfileFields();
            }
            ?>
            <?php if (!isset($fieldsArray[$modelType][$userUsing][$editingUser])): ?>
                <?php 
                    $editingUser = 'user';
                ?>
            <?php endif ?>
            <?php foreach ($fieldsArray[$modelType][$userUsing][$editingUser]['fields'] as $key => $value): ?>
                <?php
                    $fieldsStoredAllFormBuilderRows = [];
                    $i= 0; // row if togerter mean 1 row x columns
                ?>
                <?php foreach ($value as $key2 => $value2): ?>
                    <?php if (!isset($value2['next'])): ?>
                            <?php $i++; ?>
                        <?php else: ?>
                    <?php endif ?>
                    <?php 
                        $fieldsStoredAllFormBuilderRows[$i]['attributes'][$key2] = $value2['field'];
                    ?>
                <?php endforeach ?>
                <?= FormGrid::widget([
                    'model' => $model->getModel($key),
                    'form' => $form,
                    'rows' => $fieldsStoredAllFormBuilderRows,
                ]) ?>
            <?php endforeach ?>
			*/ ?>
        </div>

		<?php /*
        <?php if (Yii::$app->getModule('user')->profileAddressFields): ?>
            <div class="form-panel.no-padding">
                <h2 class="heading heading-underline">Contact Information</h2>

                <?= Address::widget([
                    'form' => $form,
                    'mode' => 'advance',
                    'model' => $model->getModel('address'),
                ]); ?>
                <br/><br/>
            </div>
        <?php endif ?>
		*/?>

        <div class="form-group">
            <?= Html::submitButton('Update Profile', ['class' => 'btn btn-primary', 'name' => 'update-profile-button']) ?>
        </div>

    <?php ActiveForm::end() ?>
</div>