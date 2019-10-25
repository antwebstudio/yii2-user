<?php

return [
    'id' => 'address',
	/*'alias' => [
		'@frontend/modules/address' => dirname(dirname(dirname(__DIR__))).'/frontend/modules/address',
		'@common/modules/address' => dirname(dirname(dirname(__DIR__))).'/common/modules/address',
		'@backend/modules/address' => dirname(dirname(dirname(__DIR__))).'/backend/modules/address',
	],*/
    'class' => \ant\address\Module::className(),
    'isCoreModule' => false,
	'depends' => [],
];
?>