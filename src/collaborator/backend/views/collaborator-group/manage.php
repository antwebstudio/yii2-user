<?php
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\web\JsExpression;

$this->context->layout = '//one-column';
?>
<h2>Manage Collaborator: <?= $model->getCollaboratorGroup()->model->name ?></h2>

<?php if (YII_DEBUG): ?>
    <?php
        /*$organizer = \ant\event\models\Organizer::find()->joinWith('collaboratorGroup group')->andWhere([
            'group.id' => $model->collaboratorGroup,
        ])->one();
*/
        //echo $organizer->haveCollaborator(3) ? 'y':'n';
    ?>
<?php endif ?>

<?php $form = ActiveForm::begin() ?>
    <?= $form->field($model, 'user')->widget(\kartik\select2\Select2::className(), [
        //'initValueText' => 'kartik-v/yii2-widgets',
        'options' => ['placeholder' => 'Search for a user ...'],
        'pluginOptions' => [
            'allowClear' => true,
            'minimumInputLength' => 1,
            'ajax' => [
                'url' => Url::to(['/collaborator/collaborator-group/ajax-users']),
                'dataType' => 'json',
                'delay' => 250,
                'data' => new JsExpression('function(params) { return {q:params.term, page: params.page}; }'),
                'processResults' => new JsExpression('function (data, params) {
                    params.page = params.page || 1;
                    return {
                        results: data,
                        pagination: {
                            more: (params.page * 30) < data.total_count
                        }
                    };
                }'),
                'cache' => true
            ],
            //'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
            'templateResult' => new JsExpression('function (result) {
                return result.username;

                console.log(result);
                if (result.loading) {
                    return result.username;
                }
            }'),
            'templateSelection' => new JsExpression('function(result) {
                return result.username;
            }'),
        ],
    ]) ?>
    <?= Html::submitButton('Add', ['class' => 'btn btn-primary']) ?>
<?php ActiveForm::end() ?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'columns' => [
        'username',
        [
            'class' => 'yii\grid\ActionColumn',
            'template' => '{delete}',
            'visibleButtons' => [
                'delete' => $dataProvider->totalCount > 1,
            ],
            'urlCreator' => function ($action, $model, $key, $index) {
                if ($action == 'delete') {
                    $mapId = $model->collaboratorGroupMapId;
                    return Url::to(['/collaborator/backend/collaborator-group/delete-collaborator', 'id' => $mapId]);
                }
            },
        ]
    ]
]) ?>