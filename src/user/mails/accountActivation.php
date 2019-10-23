<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $user common\models\User */
/* @var $activationCode */
/* @var $activationLink */

?>
<div class="mail-account-activation">
    <p>Hello <?= Html::encode($user->username) ?>,</p>

    <p>Activation Code: <?= $activationCode ?></p>

    <p>Follow the link below to activate your account:</p>

    <p><?= Html::a(Html::encode($activationLink), $activationLink) ?></p>
</div>
