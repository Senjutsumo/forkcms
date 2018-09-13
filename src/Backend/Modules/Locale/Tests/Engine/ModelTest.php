<?php

namespace Backend\Modules\Locale\Tests\Engine;

use Backend\Modules\Locale\DataFixtures\LoadLocale;
use Backend\Modules\Locale\Engine\Model;
use Common\WebTestCase;

final class ModelTest extends WebTestCase
{
    public function setUp(): void
    {
        parent::setUp();

        if (!defined('APPLICATION')) {
            define('APPLICATION', 'Backend');
        }

        $client = self::createClient();
        $this->loadFixtures(
            $client,
            [
                LoadLocale::class,
            ]
        );
    }

    public function testConstantTypes(): void
    {
        $typeArray = ['act', 'err', 'lbl', 'msg'];
        $this->assertSame($typeArray, Model::TYPES);

        $typeArray = ['act', 'test', 'lbl', 'msg'];
        $this->assertNotSame($typeArray, Model::TYPES);
    }

    public function testGet(): void
    {
        // TODO: Change assertEquals to assertSame after switching to doctrine
        $this->assertEquals(
            LoadLocale::backendCoreErrorData(),
            Model::get(LoadLocale::backendCoreErrorData()['id'])
        );

        $this->assertEquals(
            LoadLocale::backendCoreErrorData(),
            Model::get(LoadLocale::backendCoreErrorData()['id'])
        );

        $urlencoded =  urlencode(LoadLocale::backendCoreActionData()['value']);
        $this->assertSame(LoadLocale::backendCoreActionData()['value'], urldecode($urlencoded));
    }

    public function testExists(): void
    {
        $this->assertFalse(Model::exists(9999999));
        $this->assertTrue(Model::exists(LoadLocale::backendCoreErrorData()['id']));
    }

    public function testExistsByName(): void
    {
        $this->assertTrue(
            Model::existsByName(
                LoadLocale::backendCoreActionData()['name'],
                LoadLocale::backendCoreActionData()['type'],
                LoadLocale::backendCoreActionData()['module'],
                LoadLocale::backendCoreActionData()['language'],
                LoadLocale::backendCoreActionData()['application']
            )
        );

        $this->assertFalse(
            Model::existsByName(
                LoadLocale::backendCoreActionData()['name'],
                LoadLocale::backendCoreActionData()['type'],
                LoadLocale::backendCoreActionData()['module'],
                LoadLocale::backendCoreActionData()['language'],
                LoadLocale::backendCoreActionData()['application'],
                LoadLocale::backendCoreActionData()['id']
            )
        );

        $this->assertTrue(
            Model::existsByName(
                LoadLocale::backendCoreActionData()['name'],
                LoadLocale::backendCoreActionData()['type'],
                LoadLocale::backendCoreActionData()['module'],
                LoadLocale::backendCoreActionData()['language'],
                LoadLocale::backendCoreActionData()['application'],
                9999999
            )
        );

        $this->assertFalse(
            Model::existsByName(
                'TestWrongName',
                'act',
                'Core',
                'en',
                'Frontend'
            )
        );
    }

    public function testInsert(): void
    {
        $id = 9900;

        // Check if ID is new
        $this->assertFalse(Model::exists($id));

        // insert new data with action type
        $insertedId = Model::insert(
            LoadLocale::getLocaleRecord(
                $id,
                'Frontend',
                'Core',
                'act',
                'TestInsertedAction',
                LoadLocale::getEncodedTestValue()
            )
        );

        // compare new id with the insertedId and see if it exists in the database
        $this->assertSame($id, $insertedId);
        $this->assertTrue(Model::exists($id));

        // is the value url encoded
        $this->assertSame(LoadLocale::getUrlTestValue(), Model::get(9900)['value']);

        $id = 9901;

        $this->assertFalse(Model::exists($id));

        // insert new data without action type
        $insertedId = Model::insert(
            LoadLocale::getLocaleRecord(
                $id,
                'Frontend',
                'Core',
                'lbl',
                'TestInsertedLabel',
                LoadLocale::getEncodedTestValue()
            )
        );

        $this->assertSame($id, $insertedId);
        $this->assertTrue(Model::exists($id));
    }

    public function testUpdate(): void
    {
        // values to use as update data
        $newName = 'FrontendCoreMessageUpdated';
        $newValue = 'frontend core message value updated';

        // Be sure that the id exists before update
        $this->assertTrue(Model::exists(LoadLocale::frontendCoreMessageData()['id']));

        // Compare if name and value are different
        $this->assertNotSame($newName, LoadLocale::frontendCoreMessageData()['name']);
        $this->assertNotSame($newValue, LoadLocale::frontendCoreMessageData()['value']);

        $updatedRows = Model::update(
            LoadLocale::getLocaleRecord(
                LoadLocale::frontendCoreMessageData()['id'],
                LoadLocale::frontendCoreMessageData()['application'],
                LoadLocale::frontendCoreMessageData()['module'],
                LoadLocale::frontendCoreMessageData()['type'],
                $newName,
                $newValue
            )
        );

        // We expect the updated row count to be 1
        $this->assertSame(1, $updatedRows);

        // The inserted values need to be the same as the new name and value
        $this->assertSame($newName, Model::get(LoadLocale::frontendCoreMessageData()['id'])['name']);
        $this->assertSame($newValue, Model::get(LoadLocale::frontendCoreMessageData()['id'])['value']);

        // Update the same data again to see if the updated rows are 0
        $updatedRows = Model::update(
            LoadLocale::getLocaleRecord(
                LoadLocale::frontendCoreMessageData()['id'],
                LoadLocale::frontendCoreMessageData()['application'],
                LoadLocale::frontendCoreMessageData()['module'],
                LoadLocale::frontendCoreMessageData()['type'],
                $newName,
                $newValue
            )
        );

        $this->assertSame(0, $updatedRows);

        // Try to update a non existing ID
        $updatedRows = Model::update(
            LoadLocale::getLocaleRecord(
                999999999,
                LoadLocale::frontendCoreMessageData()['application'],
                LoadLocale::frontendCoreMessageData()['module'],
                LoadLocale::frontendCoreMessageData()['type'],
                $newName,
                $newValue
            )
        );

        $this->assertSame(0, $updatedRows);

        // Update to test the encoding functionality
        $newName = 'FrontendCoreActionUpdated';
        $newValue = LoadLocale::getEncodedTestValue();

        $this->assertTrue(Model::exists(LoadLocale::frontendCoreActionData()['id']));
        $this->assertNotSame($newName, LoadLocale::frontendCoreActionData()['name']);
        $this->assertNotSame($newValue, LoadLocale::frontendCoreActionData()['value']);

        $updatedRows = Model::update(
            LoadLocale::getLocaleRecord(
                LoadLocale::frontendCoreActionData()['id'],
                LoadLocale::frontendCoreActionData()['application'],
                LoadLocale::frontendCoreActionData()['module'],
                LoadLocale::frontendCoreActionData()['type'],
                $newName,
                $newValue
            )
        );

        $this->assertSame(1, $updatedRows);
        $this->assertSame($newName, Model::get(LoadLocale::frontendCoreActionData()['id'])['name']);

        // Check if actions are encoded
        $this->assertSame(
            LoadLocale::getUrlTestValue(),
            Model::get(LoadLocale::frontendCoreActionData()['id'])['value']
        );
    }

    public function testDelete(): void
    {
        $id = LoadLocale::backendCoreActionData()['id'];
        $this->assertTrue(Model::exists($id));

        Model::delete([$id]);
        $this->assertFalse(Model::exists($id));
    }

    public function testGetByName(): void
    {
        $this->assertSame(
            LoadLocale::backendCoreErrorData()['id'],
            Model::getByName(
                LoadLocale::backendCoreErrorData()['name'],
                LoadLocale::backendCoreErrorData()['type'],
                LoadLocale::backendCoreErrorData()['module'],
                LoadLocale::backendCoreErrorData()['language'],
                LoadLocale::backendCoreErrorData()['application']
            )
        );
    }

    public function testGetTypeName(): void
    {
        $this->assertSame('action', Model::getTypeName('act'));
        $this->assertSame('error', Model::getTypeName('err'));
        $this->assertSame('label', Model::getTypeName('lbl'));
        $this->assertSame('message', Model::getTypeName('msg'));
        $this->assertNotSame('action', Model::getTypeName('msg'));
        $this->assertNotSame('error', Model::getTypeName('lbl'));
        $this->assertNotSame('label', Model::getTypeName('act'));
        $this->assertNotSame('message', Model::getTypeName('err'));
    }

    public function testGetTranslations(): void
    {
        // Retrieve existing translations from the database
        $resultArray = Model::getTranslations(
            'Frontend',
            'Core',
            ['act', 'err', 'lbl', 'msg'],
            ['en'],
            'Frontend',
            'frontend core'
        );

        // Compare the retrieved data from the database with the test data
        $this->assertSame(LoadLocale::frontendCoreLabelData()['name'], $resultArray['lbl'][0]['name']);
        $this->assertSame(LoadLocale::frontendCoreLabelData()['value'], $resultArray['lbl'][0]['en']);
        $this->assertNotSame(LoadLocale::backendModuleActionData()['name'], $resultArray['lbl'][0]['name']);
        $this->assertNotSame(LoadLocale::backendModuleActionData()['value'], $resultArray['lbl'][0]['en']);

        // Make sure new languages arrays are set as empty
        $resultArrayNewLanguage = Model::getTranslations(
            'Frontend',
            'Core',
            ['act', 'err', 'lbl', 'msg'],
            ['en', 'nl'],
            'Frontend',
            'frontend core'
        );

        $this->assertEmpty($resultArrayNewLanguage['lbl'][0]['nl']);
        $this->assertEmpty($resultArrayNewLanguage['lbl'][0]['translation_id_nl']);
    }

    public function testGetLanguagesForMultiCheckbox(): void
    {
        $languages = [
            'en' => [
                'value' => 'en',
                'label' => '{$lblCoreEN}',
            ],
            'nl' => [
                'value' => 'nl',
                'label' => '{$lblCoreNL}',
            ],
        ];

        $this->assertSame($languages, Model::getLanguagesForMultiCheckbox());
        $this->assertSame($languages, Model::getLanguagesForMultiCheckbox(true));
    }
}
