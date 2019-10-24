<?php 
use yii\grid\GridView;
use yii\helpers\Html;
use ant\user\models\UserInvite;
use ant\user\models\User;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
?>

<?php
$this->title = 'Invite List';
$this->params['breadcrumbs'][] = $this->title;
$this->params['content-header-buttons'][] = Html::a('<span class="btn-label"><i class="fa fa-plus"></i></span> Create New Invite', ['create'], ['class' => 'btn btn-sm btn-primary btn-labeled']);
?>
<div clas='table-responsive'>
	<div class="panel panel-default">
	<div class="panel-body">
		<?= GridView::widget([
    'dataProvider' => $dataProvider,
	'tableOptions' => ['class' => 'table table-striped table-bordered align-middle'],
	'options' => ['class' => 'table-responsive'],
    'columns' => 
    [
	    [
		    'label' => 'ID',
		    'attribute' => 'id',
			'headerOptions' => ['class' => 'min-width'],
	    ],
	    [
		    'label' => 'Role',
		    'attribute' => 'role',
			'headerOptions' => ['class' => 'min-width'],
	    ],
	    [
		    'label' => 'Email Receiver',
			'attribute' => 'email',
	    	'headerOptions' => ['class' => 'min-width'],
	    	'format' => 'html',
	    	'content' => function($model){
	    		if (strlen($model->email) > 40) {
	    				return '<span title="'.$model->email.'">'.substr($model->email, 0, 40).'...</span>';
	    		}
	    		return $model->email;
	    	}
	    	,
	    ],
	    [
		    'label' => 'Email Registered',
		    'attribute' => 'userEmail',
		    'format' => 'html',
		   	'value' => function($model){
	    		if (isset($model->user)) {
	    			if (strlen($model->user->email) > 40) {
	    				return '<span title="'.$model->user->email.'">'.substr($model->user->email, 0, 40).'...</span>';
	    			}
	    			else return null;
	    		}
	    	},
	    	'headerOptions' => ['class' => 'min-width'],
	    ],
			    //['label' => 'Firstname',
			    //'attribute' => 'data.firstname',
			    // 'visible' => isset(yii::$app->getModule('user')->inviteSetting) ? 
			    // 	isset(yii::$app->getModule('user')->inviteSetting['firstname']) ? yii::$app->getModule('user')->inviteSetting['firstname'] : false 
			    // 	: false,
    			// 	'headerOptions' => ['class' => 'min-width'],
			    //],
			    // ['label' => 'Lastname',
			    // 'attribute' => 'data.lastname',
    			// 	'headerOptions' => ['class' => 'min-width'],
			    // 'visible' => isset(yii::$app->getModule('user')->inviteSetting) ? 
			    // 	isset(yii::$app->getModule('user')->inviteSetting['lastname']) ? yii::$app->getModule('user')->inviteSetting['lastname']  : false 
			    // 	:false,
			    // ],
			    // ['label' => 'Contact',
			    // 'attribute' => 'data.contact',
    			// 	'headerOptions' => ['class' => 'min-width'],
			    // 'visible' => isset(yii::$app->getModule('user')->inviteSetting) ? 
			    // 	isset(yii::$app->getModule('user')->inviteSetting['contact']) ? yii::$app->getModule('user')->inviteSetting['contact'] : false 
			    // 	:false,
			    // ],
			    // ['label' => 'Company Name',
			    // 'attribute' => 'data.company',
    			// 	'headerOptions' => ['class' => 'min-width'],
			    // 'visible' => isset(yii::$app->getModule('user')->inviteSetting) ? 
			    // 	isset(yii::$app->getModule('user')->inviteSetting['company']) ? yii::$app->getModule('user')->inviteSetting['company'] : false 
			    // 	:false,
			    // ],
			    // ['label' => 'Discount Rate',
			    // 'attribute' => 'data.discount_rate',
    			// 	'headerOptions' => ['class' => 'min-width'],
			    // 'visible' => isset(yii::$app->getModule('user')->inviteSetting) ? 
			    // 	isset(yii::$app->getModule('user')->inviteSetting['discount_rate'])? yii::$app->getModule('user')->inviteSetting['discount_rate'] :false
			    // 	: false,
			    // ],
	    [	
	    	'label' => 'Status',
		    'attribute' => 'status',
	    	'headerOptions' => ['class' => 'min-width'],
		    'contentOptions' => function($model)
			    {
				    if($model->status == UserInvite::STATUS_ACTIVATED)
				    	return ['class' => 'glyphicon glyphicon-ok text-nowrap','style'=>'color:green'];
				    else
				    	return ['class' => 'glyphicon glyphicon-remove ','style'=>'color:red'];
				},
		 
			'content'=>function($model)
				{
					if($model->status == UserInvite::STATUS_ACTIVATED)
						return " " . '<div class = "label label-default label-success"> Completed </div>';

					else
		    			return " " . '<div class = "label label-default label-danger"> Incomplete </div>';
				},
	    ],
	    // ['label' => 'Expire date',
	    // 'attribute' => 'tokens.expire_at'
	    // ],
	    ['class' => 'yii\grid\ActionColumn',
			'headerOptions' => ['class' => 'min-width'],
			'template'=>' {resendLink} {delete} {update} ',
			'contentOptions' => ['class' => 'text-right text-nowrap'],
			'header' => 'Actions',
		 'buttons' => 
		 	[
                'resendLink' => function($url, $model, $key) 
                {     
                	if($model->status == UserInvite::STATUS_NOT_ACTIVATED)
                	{
		                return Html::a('Resend Invite', ['invite/resend', 'id' => $model->id], ['class' => 'btn btn-primary btn-xs', 'data-method' => 'POST'] ); 
                	}
                	else
                		return html::encode('');
                },
                'update' => function ($url, $model, $key)
                {
                	if($model->status == UserInvite::STATUS_NOT_ACTIVATED)
                	{
                		return '<a href="'. Url::to(['invite/update', 'id' => $model->id ]) .'"><span class="glyphicon glyphicon-pencil"></span></a>';
                	}
                	else
                		return html::encode('');
                }
            ]
		],

    ],
    // 'rowOptions' => function ($model, $index, $widget, $grid){

    // 	if($model->status ==1)
    // 		return ['class' => 'danger'];

    // }

		]) ; ?>
	</div>
	</div>
</div>
