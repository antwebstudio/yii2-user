<?php 
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use unclead\multipleinput\MultipleInput;
?>

<?php
$this->title = 'Update User';

$this->params['breadcrumbs'][] = $this->title;
?>

<?= $this->render('../user/_profileTab', ['id' => $formModel->userId]) ?>

<div clas='table-responsive'>
<?php $form = ActiveForm::begin(['action' => ['', 'id' => $id, 'inviteType' => $inviteType ], 'id' => 'request-invite-form']); ?>
    <?php if ($formModel->hasErrors()): ?>
        <?= \yii\bootstrap\Alert::widget([
          'options' => ['class' => 'alert alert-danger'],
          'body' => $form->errorSummary($formModel),
        ]) ?>
    <?php endif; ?>
  
    <?php if ($formModel->currentRole == 'developer'): ?>
        <b> Roles </b>
        <br/>
        Developer
    <?php else: ?>
       <?= $form->field($formModel, 'currentRole')->dropDownList($formModel->rolesChoice, ['prompt'=>'Select a role'])->label('Roles') ?>
    <?php endif ?>

        <?php if (isset(Yii::$app->getModule('user')->inviteModel[$formModel->inviteType]['fields'])): ?>

            <?php foreach (Yii::$app->getModule('user')->inviteModel[$formModel->inviteType]['fields'] as $configFileConfigName => $configArray): ?>
                <?php if ($configArray['show']['config'] == 'true'): ?>
                    <?php
                        $configValue = null;
                    ?>
                    <?php foreach ($formModel->models as $config): ?>
                      <?php if ($configFileConfigName == $config->config_name): ?>
                        <!-- set value to this config, and break; -->
                        <?php $configValue = $config->value;
                            break;
                        ?>
                      <?php endif ?>
                    <?php endforeach ?>
                    <?php
                        $inputType = isset(Yii::$app->getModule('user')->inviteModel[$formModel->inviteType]['fields'][$configFileConfigName]['field']['type']) ? Yii::$app->getModule('user')->inviteModel[$formModel->inviteType]['fields'][$configFileConfigName]['field']['type'] : 'textInput';
                        $label = isset(Yii::$app->getModule('user')->inviteModel[$formModel->inviteType]['fields'][$configFileConfigName]['field']['label']) ? Yii::$app->getModule('user')->inviteModel[$formModel->inviteType]['fields'][$configFileConfigName]['field']['label'] : ucwords(str_replace('_', ' ', $configFileConfigName));;
                        $fieldOption = isset(Yii::$app->getModule('user')->inviteModel[$formModel->inviteType]['fields'][$configFileConfigName]['field']['fieldOption']) ? Yii::$app->getModule('user')->inviteModel[$formModel->inviteType]['fields'][$configFileConfigName]['field']['fieldOption'] : [];
                        $inputOption = isset(Yii::$app->getModule('user')->inviteModel[$formModel->inviteType]['fields'][$configFileConfigName]['field']['inputOption']) ? Yii::$app->getModule('user')->inviteModel[$formModel->inviteType]['fields'][$configFileConfigName]['field']['inputOption'] : [];
                    ?>
                    <?php if ($inputType == 'widget'): ?>
                        <?php
                          $fieldClassName = Yii::$app->getModule('user')->inviteModel[$formModel->inviteType]['fields'][$configFileConfigName]['field']['className'];

                          if (!is_array($configValue)) {
                            //multi column stored
                            if (json_decode($configValue) && isset(json_decode($configValue, true)[0])) {
                                $json = json_decode($configValue);
                                foreach ($inputOption['columns'] as $columnsIndex => $inputOptionColumnProperty) {
                                // if config is got new column, but db of this attribute does not have, does not add this data
                                    foreach ($json as $index => $configValue) {
                                        $inputOption['data'][$index][$inputOptionColumnProperty['name']] = 
                                        $configValue->$inputOptionColumnProperty['name'];
                                    }
                                }
                            }
                                //single column stored
                            else{
                                $json = json_decode($configValue);

                                if ($json) {
                                    foreach ($json as $config_name => $arrayConfigValue) {
                                      foreach ($arrayConfigValue as $arrayConfigValueIndex => $configValue){
                                            $inputOption['data'][][$config_name] = $configValue;
                                        }
                                    }
                                }
                            }
                        }
                        ?>
                      <?= $form->field($formModel, 'models['. $configFileConfigName . '][value]', $fieldOption)->{$inputType}($fieldClassName,$inputOption)->label($label); ?>
                    <?php elseif ($inputType == 'dropDownList'): ?>
                      <?php $items = isset(Yii::$app->getModule('user')->inviteModel[$formModel->inviteType]['fields'][$configFileConfigName]['field']['items']) ? Yii::$app->getModule('user')->inviteModel[$formModel->inviteType]['fields'][$configFileConfigName]['field']['items'] : [];

                       ?>
                    <?= $form->field($formModel, 'models['. $configFileConfigName . '][value]', $fieldOption)->{$inputType}($items, $inputOption)->label($label)?>
                      
                    <?php else: ?>
                    <?=$form->field($formModel, 'models['. $configFileConfigName . '][value]', $fieldOption)->{$inputType}($inputOption)->label($label);?>
                    <?php endif ?>

                <?php endif ?>
            <?php endforeach ?>
        <?php endif ?>
        
        <div class="form-group">
            <?= Html::submitButton('Update', ['class' => 'btn btn-primary']) ?>
        </div>
</div>
<?php ActiveForm::end(); ?>