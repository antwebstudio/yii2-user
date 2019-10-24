<?php
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $user ant\models\User */

$resetLink = Yii::$app->urlManagerFrontEnd->createAbsoluteUrl(ArrayHelper::merge(['user/signin/create-invite-user'], $tokenQueryParams));
?>

Follow the link below to create your account:

<?= $resetLink ?>
