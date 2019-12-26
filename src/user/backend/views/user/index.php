<?php 
use yii\grid\GridView;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use ant\user\models\UserInvite;
use ant\grid\ActionColumn;

$this->title = 'Users';
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

<?= $this->show('create', Html::a('Create User', ['/user/backend/user/create'], ['class' => 'btn btn-primary'])) ?> 
<?= $this->show('invite', Html::a('Invite User', ['/user/backend/invite'], ['class' => 'btn btn-primary'])) ?>

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
			'class' => ActionColumn::class,
            'headerOptions' => ['class' => 'min-width'],
            'template' => '{activate} {approve} {manage}',
            'contentOptions' => ['class' => 'text-right text-nowrap'],
            'header' => 'Actions',
			'buttons' => [
				'manage' => function($url, $model, $key) {
					return ActionColumn::dropdown([
						'label' => 'Edit',
						'url' => ['/user/backend/user/update', 'id' => $key],
						'items' => [
							[
								'label' => 'Update Password', 
								'url' => ['/user/backend/user/update-password', 'id' => $model->id]
							],
							[
								'label' => 'Delete', 
								'url' => ['/user/backend/user/delete', 'id' => $model->id],
								'method' => 'post',
								'confirm' => ActionColumn::MESSAGE_DELETE_CONFIRM,
							],
						],
					]);
				},
				'view' => function($url, $model, $key) {
					return Html::a('View', $url, ['class' => 'btn-sm btn btn-default']);
				},
				'activate' => function($url, $model, $key) {
					if ($model->isActive) {
						$emailActivationButton = '';
						return Html::a('Unactivate', ['/user/backend/user/unactivate', 'id' => $model->id], ['class' => 'btn-sm btn btn-warning']);
					} else {
						return ActionColumn::dropdown([
							'label' => 'Activate',
							'color' => 'success',
							'url' => ['/user/backend/user/activate', 'id' => $model->id],
							'items' => [
								[
									'label' => 'Email Activation Code', 
									'url' => ['/user/backend/user/email-activation-code', 'id' => $model->id]
								],
							],
						]);
						return \yii\bootstrap4\ButtonDropdown::widget([
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
					return '<a href="'. Url::to(['user/backend/update?id='. $model->id. '']) .'"><span class="glyphicon glyphicon-pencil"></span></a>';
				}
			],
		],
    ],

]) ;

