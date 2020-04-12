<?= \ant\widgets\Tabs::widget([
    'options' => ['class' => 'mb-4'],
    'items' => Yii::$app->menu->getMenu(\ant\user\Module::MENU_PROFILE, ['id' => Yii::$app->user->id]),
]) ?>