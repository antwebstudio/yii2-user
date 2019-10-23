<?php 
namespace user;

use UnitTester;
use ant\user\models\UserIdentity;

class UserIdentityCest
{
    public function _before(UnitTester $I)
    {
    }

    // tests
    public function testValidate(UnitTester $I)
    {
        $type = 'ic';

        $user = $I->grabFixture('user')->getModel(0);

        $identity = new UserIdentity;
        $identity->type = $type;
        $identity->user_id = $user->id;
        $identity->value = '901203351234';

        if (!$identity->save()) throw new \Exception(print_r($identity, 1));

        $identity = new UserIdentity;
        $identity->type = $type;
        $identity->user_id = $user->id;
        $identity->value = '901203351234';

        $I->assertFalse($identity->validate());
    }
	
	public function _fixtures()
    {
        return [
            'user' => [
                'class' => \tests\fixtures\UserFixture::className(),
                'dataFile' => '@tests/fixtures/data/user.php'
            ],
        ];
    }
}
