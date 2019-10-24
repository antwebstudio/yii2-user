<?php 
use yii\grid\GridView;
use yii\helpers\Html;
use ant\user\models\UserInvite;
use ant\user\models\User;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use ant\user\models\UserConfig;
?>

<?php
$this->title = 'Registered User through Invite List';

$this->params['breadcrumbs'][] = $this->title;
// foreach ($configRecordRows as $key => $value) {
//     if ($value == true) {
//         $dataProviderExtraColumns[] = 
//         [
//             'label' => ucwords(str_replace('_', ' ', $key)),
//             'headerOptions' => ['class' => 'min-width'],
//             'visible' => $value,
//             'value' => function($model) use ($key) {
//                 $userConfigModel = UserConfig::find()->findByConfigName($key, $model->user_id);
//                 if($userConfigModel){                		
//                 	return $userConfigModel->value == null ? null : $userConfigModel->value;
//                 }
//                 else return 'Not recorded';
//             },
//         ];
//     }
// }
$columns = [];
$columns[] = 
[
    'class' => 'yii\grid\ActionColumn',
        'headerOptions' => ['class' => 'min-width'],
        'template'=>' {update} ',
        'contentOptions' => ['class' => 'text-right text-nowrap'],
        'header' => 'Actions',
    'buttons' => 
    [
        'update' => function ($url, $model, $key)
        {
            if (isset($model->user)) {
            return '<a href="'. Url::to(['config/update', 'id' => $model->user_id]) .'"><span class="glyphicon glyphicon-pencil"></span></a>';
            }
            else return Html::encode('');
        }
    ]
];

?>
<div clas='table-responsive'>
	<div class="panel panel-default">
	<div class="panel-body">
		<?= GridView::widget([
    'dataProvider' => $dataProvider,
	'tableOptions' => ['class' => 'table table-striped table-bordered align-middle'],
	'options' => ['class' => 'table-responsive'],
    'columns' => 
    ArrayHelper::merge(
    [
	    [
		    'label' => 'ID',
		    'attribute' => 'user.id',
			'headerOptions' => ['class' => 'min-width'],
	    ],
	    [
		    'label' => 'Role',
		    //'attribute' => 'role',
		    'value' => function($model) {
		    	$user = User::find()->andWhere(['id' => $model->user_id])->one();
		    	$roles = $user->roles;
		    	$arrRole = end($roles);
				return $arrRole->name;
		    },
			'headerOptions' => ['class' => 'min-width'],
	    ],
	    [
		    'label' => 'Email',
		    'attribute' => 'userEmail',
		   	'value' => function($model){
	    		if (strlen($model->user->email) > 80) {
	    				return '<span title="'.$model->email.'">'.substr($model->user->email, 0, 80).'...</span>';
	    		}
	    		return $model->email;
	    	},
	    	'headerOptions' => ['class' => 'min-width'],
	    ],
    ]
    ,$columns ),
    //array merge
		]) ; ?>
	</div>
	</div>
</div>
