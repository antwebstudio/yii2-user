<?php
use yii\helpers\Html;
use yii\grid\GridView;

use ant\widgets\Alert;

$this->title = "My Profiles";

$this->params['title'] = $this->title;

$this->params['breadcrumbs'][] = ['label' => 'Setting', 'url' => ['/user/setting']];
$this->params['breadcrumbs'][] = $this->title;

$this->params['headerRightPanel'][] = Html::a('Add Profile', $url = ['/user/profiles/create'], ['class' => 'btn btn-primary']);

$this->params['sideNav'] = $sideNav;
?>
<div class="page-user-setting-profile">
<?=Alert::widget(); ?>

	<?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
           
           //'fullName',
            [
                'attribute' => 'fullName',
                'value' => 'fullName',
                'filter' => Html::activeTextInput($searchModel, 'fullName'),                
                'format' => 'html',
            ],

            [
                'attribute' => 'email',
                'value' => 'email',
                'filter' => Html::activeTextInput($searchModel, 'email'),                
                'format' => 'html',
            ], 
                       
                       
            [
				'class' => 'yii\grid\ActionColumn',
				'visibleButtons' => ['view' => false],
			],
        ],

    ]); ?>
</div>