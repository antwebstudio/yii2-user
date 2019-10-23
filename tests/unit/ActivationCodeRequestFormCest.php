<?php

use ant\user\models\ActivationCodeRequestForm;

class ActivationCodeRequestFormCest
{
    public function _before(UnitTester $I)
    {
    }

    public function _after(UnitTester $I)
    {
    }
	
	// User attribute should be a non-safe attribute
	public function testRulesForUserAttribute(UnitTester $I, $scenario) {
		$scenario->skip('ActivationCodeRequestForm is deprecated. ');
		
		$user = $I->grabFixture('user')->getModel('inactiveUser');
		if (!isset($user)) throw new \Exception('Fixture error. ');
		
        $model = new ActivationCodeRequestForm();
		
		$I->assertFalse($model->isAttributeSafe('user'));
		
		$model->load([
			(new ActivationCodeRequestForm())->formName() => [
				'user' => $user,
			],
		]);
		
		$I->assertFalse(isset($model->user));
		
		$model->user = $user;
		
		$I->assertTrue(isset($model->user));
	}

    // tests
    public function testSend(UnitTester $I, $scenario)
    {
		$scenario->skip('ActivationCodeRequestForm is deprecated. ');
		
		$user = $I->grabFixture('user')->getModel('inactiveUser');
		if (!isset($user)) throw new \Exception('Fixture error. ');
		
        $model = new ActivationCodeRequestForm();
		$model->email = $user->email;
		
		if (!$model->validate()) throw new \Exception(print_r($model->errors, 1));
		
		$I->assertTrue($model->send());
		$I->seeEmailIsSent();
    }
	
	public function testGetMailParams(UnitTester $I, $scenario) {
		$scenario->skip('ActivationCodeRequestForm is deprecated. ');
		
		$user = $I->grabFixture('user')->getModel('inactiveUser');
		if (!isset($user)) throw new \Exception('Fixture error. ');
		
        $model = new ActivationCodeRequestForm();
		$model->email = $user->email;
		
		$mailParams = $I->invokeMethod($model, 'getMailParams');
		
		$I->assertTrue(isset($mailParams['user']));
		$I->assertTrue(isset($mailParams['activationLink']));
		$I->assertTrue(isset($mailParams['activationCode']));
		$I->assertEquals($user->id, $mailParams['user']->id);
	}
	
	public function testSendActivationEmail(UnitTester $I, $scenario) {
		$scenario->skip('ActivationCodeRequestForm is deprecated. ');
		
		$user = $I->grabFixture('user')->getModel('inactiveUser');
		if (!isset($user)) throw new \Exception('Fixture error. ');
		
		$I->assertTrue(ActivationCodeRequestForm::sendActivationEmail($user));
		$I->seeEmailIsSent();
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
