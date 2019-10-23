<?php 
namespace user;

use UnitTester;
use ant\user\models\UserSearch;

class UserSearchCest
{
    public function _before(UnitTester $I)
    {
    }

    // tests
    public function test(UnitTester $I)
    {
		$model = new UserSearch;
		$model->search([]);
    }
	
	public function testSearchByQuery(UnitTester $I) {
		$model = new UserSearch;
		$dataProvider = $model->searchByQuery('123');
		
		$dataProvider->getModels();
	}
}
