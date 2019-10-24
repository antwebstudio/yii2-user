<?php
namespace ant\user\controllers;


class AuthClientController extends \yii\web\Controller {
    public function actions()
    {
        return [
            'auth' => [
                'class' => 'yii\authclient\AuthAction',
                'successCallback' => [$this, 'onAuthSuccess'],
            ],
        ];
    }

    public function onAuthSuccess($client)
    {
        (new \ant\user\components\AuthClientHandler($client))->handle();
    }

    public function actionIndex() {
        /*$client = \Yii::$app->authClientCollection->getClient('facebook');
        $attributes = $client->api('/me/likes');
        throw new \Exception(print_r($attributes,1));*/
    }
}