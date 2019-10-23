<?php
// set correct script paths
$_SERVER['SERVER_NAME'] = 'localhost';
$_SERVER['SERVER_PORT'] = '80';
$_SERVER['REMOTE_ADDR'] = '::1';

require_once dirname(dirname(__DIR__)).'/vendor/autoload.php';
require_once dirname(dirname(__DIR__)).'/vendor/yiisoft/yii2/Yii.php';

Yii::setAlias('@common', dirname(dirname(__DIR__)).'/src/common');
Yii::setAlias('@tests', dirname(__DIR__));

$config = require dirname(__DIR__).'/config/unit.php';
new yii\web\Application($config);
