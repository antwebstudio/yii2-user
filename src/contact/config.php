<?php

return [
    'id' => 'contact',
    'class' => \ant\contact\Module::className(),
    'isCoreModule' => false,
	'depends' => ['address'],
];