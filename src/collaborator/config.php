<?php
return [
    'id' => 'collaborator',
    'class' => \ant\collaborator\Module::className(),
    'isCoreModule' => false,
	'modules' => [
		//'v1' => \ant\collaborator\api\v1\Module::class,
		'backend' => \ant\collaborator\backend\Module::class,
	],
	'depends' => [], 
];
