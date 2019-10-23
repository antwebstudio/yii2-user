<?php
//namespace tests\codeception\common\user;
//use tests\codeception\common\UnitTester;
use ant\user\models\UserConfig;
use ant\user\models\UserConfigForm;

class UserConfigFormCest
{
    public function _before(UnitTester $I)
    {
    }

    public function _after(UnitTester $I)
    {
    }

    // tests
    public function testInitAvailableConfigs(UnitTester $I)
    {
        $key = 'testConfig';
        $value = 'testConfig value';

        $user = $I->grabFixture('user')->getModel(0);
        UserConfig::set($user->id, $key, $value);

        $model = new UserConfigForm([
            'user' => $user,
			'validConfigs' => [$key],
        ]);
        $I->assertEquals($value, $model->configs[$key]);
    }
	
	public function testSave(UnitTester $I) {
		$configs = [
			'config1' => 'test value',
			'key1' => 'test value2',
		];
		$keys = array_keys($configs);
		
		$data = [
			'UserConfigForm' => [
				'configs' => $configs,
			],
		];
		
        $user = $I->grabFixture('user')->getModel(0);
        UserConfig::set($user->id, $keys[0], $configs[$keys[0]]); // Make 1 config already exist
		
		$form = new UserConfigForm(['user' => $user, 'validConfigs' => $keys]);
		$I->assertTrue($form->load($data));
		if (!$form->save()) throw new \Exception(print_r($form->errors, 1));
		
		//throw new \Exception($I->renderDbTable('{{%user_config}}'));
		
		$I->assertEquals($configs[$keys[0]], UserConfig::get($user->id, $keys[0]));
		$I->assertEquals($configs[$keys[1]], UserConfig::get($user->id, $keys[1]));
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