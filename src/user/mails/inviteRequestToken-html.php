<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $user common\models\User */
/* @var $token string */

$resetLink = Yii::$app->urlManagerFrontEnd->createAbsoluteUrl(ArrayHelper::merge(['user/signin/create-invite-user'], $tokenQueryParams));
?>
<div class="mail-password-reset">

Follow the link below to create your account:

<?php echo Html::a(Html::encode($resetLink), $resetLink) ?>
</div>
