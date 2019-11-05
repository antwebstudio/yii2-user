<?php 
use yii\grid\GridView;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use ant\user\models\UserInvite;

$this->title = 'Request invite form';
$this->params['page-header']['title'] = $this->title;
$this->params['page-header']['breadcrumbs'][] = $this->title;

$gridColumns = [
	['class' => 'yii\grid\SerialColumn'],
	'email',
	[
		'label' => 'Firstname',
		'attribute' => 'profile.firstname',
	],
	[
		'label' => 'Lastname',
		'attribute' => 'profile.lastname',
	],
];
?>

<?= $this->show('create', Html::a('Create User', ['/user/user/create'], ['class' => 'btn btn-primary'])) ?>
<?= $this->show('invite', Html::a('Invite User', ['/user/invite'], ['class' => 'btn btn-primary'])) ?>

<?php if (class_exists('kartik\export\ExportMenu')): ?>
	<?= \kartik\export\ExportMenu::widget([
		'dataProvider' => $dataProvider,
		'columns' => $gridColumns,
		//'exportColumnsView' => '@backend/modules/user/views/user/_gridColumn',
		'target'=>ExportMenu::TARGET_BLANK,
		'exportConfig' => [],
	]) ?>
<?php endif ?>

<?= GridView::widget([
	'dataProvider' => $dataProvider,
	'filterModel' => $model,
    'columns' => 
    [
	    [
            'label' => 'ID',
    	    'attribute' => 'id'
	    ],
	    [
            'label' => 'Email',
			'attribute' => 'email',
	    ],
        [
            'attribute' => 'username',
		],
		[
			'attribute' => 'fullname',
		],
        [
            'label' => 'Role',
			'value' => function($model) {
				$roles = [];
				foreach (\Yii::$app->authManager->getRolesByUser($model->id) as $role) {
					if (!in_array($role->name, ['guest'])) {
						$roles[] = $role->name;
					}
				}
				return implode(', ', $roles);
			},
        ],
		[
			'label' => 'Status',
			'value' => function($model) {
				if ($model->isSignupByInvite) $status[] = 'By Invite';
				$status[] = $model->isApproved ? 'Approved' : 'Not Approved';
				
				return implode(', ', $status);
			},
			'visible' => $this->context->showApproveAction,
		],
        [
			'class' => 'yii\grid\ActionColumn',
            'headerOptions' => ['class' => 'min-width'],
            'template' => '{activate} {approve} {view} {update} {delete} ',
            'contentOptions' => ['class' => 'text-right text-nowrap'],
            'header' => 'Actions',
			'buttons' => [
				'view' => function($url, $model, $key) {
					return Html::a('View', $url, ['class' => 'btn-sm btn btn-default']);
				},
				'activate' => function($url, $model, $key) {
					if ($model->isActive) {
						$emailActivationButton = '';
						return Html::a('Unactivate', ['/user/user/unactivate', 'id' => $model->id], ['class' => 'btn-sm btn btn-warning']);
					} else {
						return \yii\bootstrap\ButtonDropdown::widget([
							'label' => 'Activate',
							'split' => true,
							'tagName' => 'a', // Needed so that href option work
							'options' => [
								'href' => ['/user/backend/user/activate', 'id' => $model->id],
								'class' => 'btn-sm btn btn-success',
							],
							'dropdown' => [
								'items' => [
									['label' => 'Email Activation Code', 'url' => ['/user/backend/user/email-activation-code', 'id' => $model->id]],
								],
							],
						]);
						$emailActivationButton = Html::tag('li', Html::a('Email Activation Code', ['/user/backend/user/email-activation-code', 'id' => $model->id], ['class' => 'btn-sm btn btn-warning']));
						$buttons = $emailActivationButton.Html::tag('li', Html::a('Activate', ['/user/backend/user/unactivate', 'id' => $model->id], ['class' => 'btn-sm btn btn-success']));
						return Html::tag('ul', $buttons, ['class' => 'dropdown-menu']);
					}
				},
				'approve' => function($url, $model, $key) {
					if (!$this->context->showApproveAction) return '';
					
					if ($model->isApproved) {
						return Html::a('Unapprove', ['/user/backend/user/unapprove', 'id' => $model->id], ['class' => 'btn-sm btn btn-warning']);
					} else {
						return Html::a('Approve', ['/user/backend/user/approve', 'id' => $model->id], ['class' => 'btn-sm btn btn-success']);
					}
				},
				'update' => function ($url, $model, $key)
				{
					return '<a href="'. Url::to(['user/update?id='. $model->id. '']) .'"><span class="glyphicon glyphicon-pencil"></span></a>';
				}
			],
		],
    ],

]) ;

