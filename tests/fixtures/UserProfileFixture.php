<?php

namespace tests\fixtures;

use yii\test\ActiveFixture;

/**
 * User fixture
 */
class UserProfileFixture extends ActiveFixture
{
    public $modelClass = 'ant\user\models\UserProfile';
	public $depends = [
        'tests\fixtures\UserFixture',
    ];
}
