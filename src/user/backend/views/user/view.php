<?php
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap\Nav;

$this->title = 'User Profile - '.$model->username;

$attributes = [
    'firstname',
    'lastname',
    'email',
    'contact_number',
    'user.created_at',
];

if (isset($model->profile->attachments)) {
    $attributes[] = [
        'attribute' => 'attachments',
        'format' => 'raw',
        'value' => isset($model->profile->attachments[0]['name']) ? Html::a($model->profile->attachments[0]['name'], $model->profile->attachments[0]['base_url'].'/'.$model->profile->attachments[0]['path'], ['target' => '_blank']) : '',
    ];
}

?>
<?= Nav::widget([
    'options' => [
        'class' => 'nav-tabs',
        'style' => 'margin-bottom: 15px'
    ],
    'items' => \Yii::$app->menu->getMenu(\ant\user\Module::MENU_VIEW_PROFILE, ['user' => $model]),
]) ?>

<?= DetailView::widget([
    'model' => $model->profile,
    'attributes' => $attributes,
]) ?>