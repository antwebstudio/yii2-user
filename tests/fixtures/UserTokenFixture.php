<?php

namespace tests\fixtures;

use yii\test\ActiveFixture;

/**
 * User fixture
 */
class UserTokenFixture extends ActiveFixture
{
    public $modelClass = 'ant\token\models\Token';
	public $depends = [
        'tests\fixtures\UserFixture',
    ];
}
