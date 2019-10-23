<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $user ant\models\User */

//$activateLink = Yii::$app->urlManagerFrontEnd->createAbsoluteUrl(ArrayHelper::merge(['user/signin/new-password-activate'], $token->queryParams));
?>
<div class="mail-account-activation">
    <p>Hi <?= Html::encode($user->emailDisplayName) ?>,</p>

    <p>Welcome, your account for <?= Yii::$app->name ?> is created.</p>
</div>
