<?php
//namespace tests\codeception\common\user;
//use tests\codeception\common\UnitTester;
use ant\user\models\CreateUserForm;
use ant\user\models\User;

class CreateUserFormCest
{
    public function _before(UnitTester $I)
    {
    }

    public function _after(UnitTester $I)
    {
    }

    // tests
    public function testSave(UnitTester $I)
    {
        $email = 'test@example.com';

        $model = new CreateUserForm;
        $model->email = $email;
        if (!$model->save()) throw new \Exception(print_r($model->errors, 1));

        $user = User::findOne($model->user->id);

        $I->assertEquals($email, $user->username);
        $I->assertEquals($email, $user->email);
    }
}
