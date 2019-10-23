<?php

return [
    'id' => 'organization',
    'class' => \ant\organization\Module::className(),
    'isCoreModule' => false,

	'depends' => ['address', 'token'],
];
?>