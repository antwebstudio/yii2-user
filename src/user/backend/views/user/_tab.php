<?php 
use ant\widgets\Nav;
?>

<?php //if (Yii::$app->getModule('user')->profileTab): ?>
<?= Nav::widget([
	'activateParents' => true,
    'options' => [
        'class' => 'nav-tabs',
        'style' => 'margin-bottom: 15px'
    ],
    'items' => \Yii::$app->menu->getMenu(\ant\user\Module::MENU_PROFILE, [
		'id' => $id,
	]),
]) ?>
<?php //endif ?>