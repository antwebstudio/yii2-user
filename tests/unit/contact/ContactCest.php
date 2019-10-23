<?php
//namespace tests\codeception\common\contact;

use yii\helpers\Html;
//use tests\codeception\common\UnitTester;
use ant\contact\models\Contact;
use ant\address\models\Address;

class ContactCest
{
    public function _before(UnitTester $I)
    {
    }

    public function _after(UnitTester $I)
    {
    }

    // tests
    public function testSetCustomState(UnitTester $I)
    {
        $contact = new Contact;
        $contact->customState = 'Penang';
        if (!$contact->save()) throw new \Exception(Html::errorSummary($contact));

        $address = Address::findOne($contact->address_id);
        $I->assertTrue(isset($address));
        $I->assertEquals('Penang', $address->custom_state);
    }

    public function testGetCustomState(UnitTester $I)
    {
        $contact = new Contact;
        $contact->customState = 'Penang';
        
        $I->assertEquals('Penang', $contact->customState);

        if (!$contact->save()) throw new \Exception(Html::errorSummary($contact));

        $I->assertEquals('Penang', $contact->customState);

        $contact = Contact::findOne($contact->id);

        $I->assertEquals('Penang', $contact->customState);
    }

    public function testSetAddressString(UnitTester $I)
    {
        $contact = new Contact;
        $contact->addressString = 'Penang';
        if (!$contact->save()) throw new \Exception(Html::errorSummary($contact));

        $address = Address::findOne($contact->address_id);
		$I->assertTrue(isset($address));
        $I->assertEquals('Penang', $address->address_1);
		
		$contact->addressString = 'Penang2';
        if (!$contact->save()) throw new \Exception(Html::errorSummary($contact));
		
		$contact = Contact::findOne($contact->id);
		$I->assertEquals('Penang2', $contact->addressString);
    }

    public function testGetAddressString(UnitTester $I)
    {
        $contact = new Contact;
        $contact->addressString = 'Penang';
        
        $I->assertEquals('Penang', $contact->addressString);

        if (!$contact->save()) throw new \Exception(Html::errorSummary($contact));

        $I->assertEquals('Penang', $contact->addressString);

        $contact = Contact::findOne($contact->id);
        
        $I->assertEquals('Penang', $contact->addressString);
    }

    public function testValidate(UnitTester $I) {
        $contact = new Contact(['scenario' => Contact::SCENARIO_BASIC_REQUIRED]);
        $I->assertFalse($contact->validate());
        $I->assertEquals(4, count($contact->errors));
    }

    public function testValidateAfterSave(UnitTester $I) {
        $contact = new Contact(['scenario' => Contact::SCENARIO_BASIC_REQUIRED]);
        $I->assertFalse($contact->save());
        $I->assertFalse($contact->validate());
        $I->assertEquals(4, count($contact->errors));
    }
	
	public function testSaveBlank(UnitTester $I) {
		$contact = new Contact;
		if (!$contact->save()) throw new \Exception(print_r($contact, 1));
		
		//$contact->address->scenario = Address::SCENARIO_DEFAULT;
		
		//$I->assertFalse($contact->address->validate());
	}
	
	public function testSaveAsNewRecordIfDirtyAddressString(UnitTester $I) {
		$contact = new Contact;
        $contact->addressString = 'Penang';
		
		if (!$contact->save()) throw new \Exception(print_r($contact, 1));
		
		$oldAddressId = $contact->address->id;
		
        $contact->addressString = 'Penang2';
		$contact->address->scenario = Address::SCENARIO_NO_REQUIRED;
		
		$I->assertTrue($contact->address->validate());
		
		$contact->saveAsNewRecordIfDirty();
		
		$newAddressId = $contact->address->id;
		
		$newAddress = Address::findOne($newAddressId);
		$oldAddress = Address::findOne($oldAddressId);
		
		$I->assertNotEquals($oldAddressId, $newAddressId);
		$I->assertEquals($oldAddressId + 1, $newAddressId);
		$I->assertEquals('Penang', $oldAddress->address_1);
		$I->assertEquals('Penang2', $newAddress->address_1);
	}
	
	public function testSaveAsNewRecordIfDirtyAddressUpdated(UnitTester $I) {
		$contact = new Contact;
		if (!$contact->save()) throw new \Exception(print_r($contact, 1));
		
        $contact->address->city = 'Penang';
		$contact->address->scenario = Address::SCENARIO_NO_REQUIRED;
		
		$oldAddressId = $contact->address->id;
		
		if (!$contact->address->save()) throw new \Exception(print_r($contact->address->errors, 1));
		
		$contact->address->city = 'Penang2';
		$contact->saveAsNewRecordIfDirty();
		
		$newAddressId = $contact->address->id;
		
		$newAddress = Address::findOne($newAddressId);
		$oldAddress = Address::findOne($oldAddressId);
		
		$I->assertNotEquals($oldAddressId, $newAddressId);
		$I->assertEquals($oldAddressId + 1, $newAddressId);
		$I->assertEquals('Penang', $oldAddress->city);
		$I->assertEquals('Penang2', $newAddress->city);
	}
	
	// Contact updated, address not updated
	public function testSaveAsNewRecordIfDirtyAddressNotUpdated(UnitTester $I) {
		$contact = new Contact;
		if (!$contact->save()) throw new \Exception(print_r($contact, 1));
		
        $contact->address->city = 'Penang';
		$contact->address->scenario = Address::SCENARIO_NO_REQUIRED;
		
		$oldAddressId = $contact->address->id;
		
		if (!$contact->address->save()) throw new \Exception(print_r($contact->address->errors, 1));
		
		$oldContactId = $contact->id;
		$contact = Contact::findOne($contact->id);
		$contact->address->scenario = Address::SCENARIO_NO_REQUIRED;
		
		$contact->organization = 'new organization';
		
		$I->assertTrue($contact->validate());
	
		$newContact = $contact->saveAsNewRecordIfDirty();
		$contact->refresh();
		
		$newAddressId = $newContact->address->id;
		
		$I->assertNotEquals($oldContactId, $newContact->id);
		$I->assertNotEquals($oldAddressId, $newAddressId);
		$I->assertEquals($oldAddressId, $contact->address->id);
		$I->assertEquals($oldAddressId + 1, $newAddressId);
	}
	
	public function testSaveAsNewRecordIfDirtyWithRequiredAddressFields(UnitTester $I) {
		$contact = new Contact;
        $contact->addressString = 'Penang';
		
		if (!$contact->save()) throw new \Exception(print_r($contact, 1));
		
		$oldAddressId = $contact->address->id;
		
		$contact->address->scenario = Address::SCENARIO_DEFAULT;
        $contact->addressString = 'Penang2';
		$contact->saveAsNewRecordIfDirty();
		
		$newAddressId = $contact->address->id;
		
		$oldAddress = Address::findOne($oldAddressId);
		
		$I->assertEquals($oldAddressId, $newAddressId);
		$I->assertEquals('Penang', $oldAddress->address_1);
		$I->assertTrue($contact->address->hasErrors());
	}
}
