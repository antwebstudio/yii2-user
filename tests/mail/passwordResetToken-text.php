<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;


/* @var $this yii\web\View */
/* @var $user ant\models\User */

$resetLink = Yii::$app->urlManager->createAbsoluteUrl(ArrayHelper::merge(['user/signin/reset-password'], $tokenQueryParams));
?>
Hello <?= $user->username ?>,

Follow the link below to reset your password:

<?= $resetLink ?>
