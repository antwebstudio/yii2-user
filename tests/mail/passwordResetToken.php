<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $user ant\models\User */
/* @var $token string */

$resetLink = Yii::$app->urlManager->createAbsoluteUrl(ArrayHelper::merge(['user/signin/reset-password'], $tokenQueryParams));
?>
<div class="mail-password-reset">
Hello <?php echo Html::encode($user->username) ?>,

Follow the link below to reset your password:

<?php echo Html::a(Html::encode($resetLink), $resetLink) ?>
</div>
