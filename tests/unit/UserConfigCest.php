<?php
//namespace tests\codeception\common\user;
//use tests\codeception\common\UnitTester;
use ant\user\models\UserConfig;

class UserConfigCest
{
    public function _before(UnitTester $I)
    {
		\Yii::configure(\Yii::$app, [
            'components' => [
				'userConfig' => [
					'class' => 'ant\user\components\UserConfig',
				],
			],
		]);
    }

    public function _after(UnitTester $I)
    {
    }

    // tests
    public function testSetterAndGetter(UnitTester $I)
    {
		$user = $I->grabFixture('user')->getModel(0);
		
		\Yii::$app->user->login($user);
		
		$testKey = 'testkey';
		$value = 'testvalue';
		
		$result = \Yii::$app->userConfig->get($testKey);
		$I->assertFalse(isset($result));
		
		\Yii::$app->userConfig->set($testKey, $value);
		
		$result = \Yii::$app->userConfig->get($testKey);
		$I->assertTrue(isset($result));
		$I->assertEquals($value, \Yii::$app->userConfig->get($testKey));
    }
	
	public function testSetExistedConfig(UnitTester $I)
    {
		$user = $I->grabFixture('user')->getModel(0);
		
		\Yii::$app->user->login($user);
		
		$testKey = 'testkey';
		$value = 'testvalue';
		$value2 = 'testvalue2';
		
		$result = \Yii::$app->userConfig->get($testKey);
		$I->assertFalse(isset($result));
		
		\Yii::$app->userConfig->set($testKey, $value);
		
		$result = \Yii::$app->userConfig->get($testKey);
		$I->assertTrue(isset($result));
		$I->assertEquals($value, \Yii::$app->userConfig->get($testKey));
		
		
		\Yii::$app->userConfig->set($testKey, $value2);
		
		$result = \Yii::$app->userConfig->get($testKey);
		$I->assertTrue(isset($result));
		$I->assertEquals($value2, \Yii::$app->userConfig->get($testKey));
    }
	
	public function testSetterAndGetterWithArray(UnitTester $I)
    {
		$user = $I->grabFixture('user')->getModel(0);
		$testkey1 = 'testkey';
		$testkey2 = 'testkey2';
		
		\Yii::$app->user->login($user);
		
		$testData = [
			$testkey1 => 'testvalue',
			$testkey2 => 'testvalue2',
		];
		
		$result = \Yii::$app->userConfig->get($testkey1);
		$I->assertFalse(isset($result));
		$result = \Yii::$app->userConfig->get($testkey2);
		$I->assertFalse(isset($result));
		$count = UserConfig::find()->andWhere(['user_id' => $user->id])->count();
		$I->assertEquals(0, $count);
		
		\Yii::$app->userConfig->set($testData);
		\Yii::$app->userConfig->clearCache();
		
		$result = \Yii::$app->userConfig->get($testkey1);
		$I->assertTrue(isset($result));
		$result = \Yii::$app->userConfig->get($testkey2);
		$I->assertTrue(isset($result));$count = UserConfig::find()->andWhere(['user_id' => $user->id])->count();
		$I->assertEquals(2, $count);
		
		$I->assertEquals($testData[$testkey1], \Yii::$app->userConfig->get($testkey1));
		$I->assertEquals($testData[$testkey2], \Yii::$app->userConfig->get($testkey2));
    }
	
	public function _fixtures()
    {
        return [
            'user' => [
                'class' => \tests\fixtures\UserFixture::className(),
                'dataFile' => '@tests/fixtures/data/user.php'
            ],
            'user_config' => [
                'class' => \tests\fixtures\UserConfigFixture::className(),
                'dataFile' => '@tests/fixtures/data/user_config.php'
            ],
        ];
    }
}
