<?php

return [
    'id' => 'user',
    'class' => \ant\user\Module::className(),
    'isCoreModule' => false,
	'modules' => [
		'v1' => \ant\user\api\v1\Module::class,
		'backend' => \ant\user\backend\Module::class,
	],
	'depends' => ['address', 'token', 'contact'],
];