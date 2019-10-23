<?php
namespace user;

use Yii;
use \UnitTester;
//use tests\codeception\frontend\unit\DbTestCase as TestCase;
use ant\user\models\PasswordResetRequestForm;
use ant\user\models\User;
use ant\token\models\Token;

class PasswordResetRequestFormCest
{

    protected function _before(UnitTester $I)
    {
        parent::setUp();

        Yii::$app->mailer->fileTransportCallback = function ($mailer, $message) {
            return 'testing_message.eml';
        };
    }

    protected function _after(UnitTester $I)
    {
        @unlink($this->getMessageFile());

        parent::tearDown();
    }

    public function _fixtures()
    {
        return [
            'user' => [
                'class' => \tests\fixtures\UserFixture::className(),
            ],
        ];
    }

    public function testSendEmailWrongUser(UnitTester $I)
    {
		$model = new PasswordResetRequestForm();
		$model->email = 'not-existing-email@example.com';

		$I->assertFalse($model->sendEmail());
		
		$model = new PasswordResetRequestForm();
		$model->email = $I->grabFixture('user')->getModel(1)['email'];

		$I->assertTrue($model->sendEmail());
    }

    public function testSendEmailCorrectUser(UnitTester $I)
    {
		$user = $I->grabFixture('user')->getModel('user');
        $model = new PasswordResetRequestForm();
        $model->email = $user['email'];
        $user = User::findOne(['username' => $user['username']]);

        $I->assertTrue($model->sendEmail());
		
		$passwordResetToken = Token::find()
            ->byUser($user)
            ->byType(Token::TOKEN_TYPE_USER_PASSWORD_RESET)
            /*->byQueryParams([
                'tokenkey' => $tokenkey,
                'email' => $model->email,
            ])*/
            ->one();
			
        $I->assertNotEquals(null, $passwordResetToken->token);
		
		$I->seeEmailIsSent();

		$emailMessage = $I->grabLastSentEmail();
		$I->assertTrue($emailMessage instanceof \yii\mail\MessageInterface);
		$I->assertTrue(array_key_exists($model->email, $emailMessage->getTo()));
		$I->assertTrue(array_key_exists($model->emailFrom, $emailMessage->getFrom()));
	
		//$message = file_get_contents($this->getMessageFile());
		//expect('message "from" is correct', $message)->contains(Yii::$app->params['supportEmail']);
		//expect('message "to" is correct', $message)->contains($model->email);
    }

    private function getMessageFile()
    {
        return Yii::getAlias(Yii::$app->mailer->fileTransportPath) . '/testing_message.eml';
    }
}
