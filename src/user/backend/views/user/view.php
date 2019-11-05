<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap\Nav;

$this->title = 'User Profile - '.$model->username;

$attributes = [
    'profile.firstname',
    'profile.lastname',
    'email',
    'profile.contact_number',
    'created_at',
];

if (isset($model->profile->attachments)) {
    $attributes[] = [
        'attribute' => 'attachments',
        'format' => 'raw',
        'value' => isset($model->profile->attachments[0]['name']) ? Html::a($model->profile->attachments[0]['name'], $model->profile->attachments[0]['base_url'].'/'.$model->profile->attachments[0]['path'], ['target' => '_blank']) : '',
    ];
}

?>
<?= $this->render('_tab', ['id' => $model->id]) ?>

<a class="btn btn-primary" href="<?= Url::to(['/user/backend/user/update', 'id' => $model->id]) ?>">Edit</a>

<?= DetailView::widget([
    'model' => $model,
    'attributes' => $attributes,
]) ?>