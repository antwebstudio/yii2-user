<?php
namespace user;
use \UnitTester;
use ant\user\models\ResetPasswordForm;
use ant\token\models\Token;

class ResetPasswordCest
{
    public function _before(UnitTester $I)
    {
    }

    public function _after(UnitTester $I)
    {
    }
	
    /**
     * @expectedException \yii\base\InvalidParamException
     */
    public function testResetWrongToken(UnitTester $I)
    {
		$user = $I->grabFixture('user')->getModel('user');
        $I->expectException(new \Exception('Wrong password reset detail.'), function() use ($user) {
			new ResetPasswordForm('notexistingtoken_1391882543', $user->email);
		});
    }

    /**
     * @expectedException \yii\base\InvalidParamException
     */
    public function testResetEmptyToken(UnitTester $I)
    {
		$user = $I->grabFixture('user')->getModel('user');
        $I->expectException(new \Exception('Password reset key cannot be blank.'), function() use ($user) {
			new ResetPasswordForm('', $user->email);
		});
    }

    public function testResetCorrectToken(UnitTester $I)
    {
		$user = $I->grabFixture('user')->getModel('user');
		//$token = $I->grabFixture('userToken')->getModel('user_token');
		$key = Token::createTokenKey();
		$token = Token::create($user, Token::TOKEN_TYPE_USER_PASSWORD_RESET,  [
			'email' => $user->email,
			'tokenkey' => $key
		]);
		
        $form = new ResetPasswordForm($key, $user->email);
		
		$I->assertTrue($form->resetPassword());
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
