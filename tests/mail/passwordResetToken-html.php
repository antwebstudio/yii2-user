<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $user ant\models\User */

$resetLink = Yii::$app->urlManager->createAbsoluteUrl(ArrayHelper::merge(['user/signin/reset-password'], $tokenQueryParams));
?>
<div class="password-reset">
    <p>Hello <?= Html::encode($user->username) ?>,</p>

    <p>Follow the link below to reset your password:</p>

    <p><?= Html::a(Html::encode($resetLink), $resetLink) ?></p>
</div>
