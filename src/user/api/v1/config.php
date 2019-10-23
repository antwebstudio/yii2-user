<?php

return [
    'id' => 'user',
    'class' => \ant\user\api\v1\Module::class,
    'isCoreModule' => false,
	'depends' => ['address', 'token'],
];