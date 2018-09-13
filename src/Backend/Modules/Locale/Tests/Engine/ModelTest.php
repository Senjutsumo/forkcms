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
                999
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
        $id = 9013;

        $this->assertFalse(Model::exists($id));

        $insertedId = Model::insert(
            LoadLocale::getLocaleRecord(
                $id,
                'Frontend',
                'Core',
                'act',
                'TestInsertedLabel',
                'frontend core action value'
            )
        );

        $this->assertSame($id, $insertedId);
        $this->assertTrue(Model::exists($id));
    }

    public function testUpdate(): void
    {
        $id = 9010;
        $name = 'FrontendCoreMessage';
        $value = 'frontend core message value';

        $this->assertTrue(Model::exists($id));
        $this->assertSame($name, LoadLocale::frontendCoreMessageData()['name']);
        $this->assertSame($value, LoadLocale::frontendCoreMessageData()['value']);

        $updatedRows = Model::update(
            LoadLocale::getLocaleRecord(
                $id,
                LoadLocale::frontendCoreMessageData()['application'],
                LoadLocale::frontendCoreMessageData()['module'],
                LoadLocale::frontendCoreMessageData()['type'],
                'FrontendCoreMessageUpdated',
                'frontend core message value updated'
            )
        );

        $this->assertSame(1, $updatedRows);
        $this->assertSame('FrontendCoreMessageUpdated', Model::get(9010)['name']);
        $this->assertSame('frontend core message value updated', Model::get(9010)['value']);

        $updatedRows = Model::update(
            LoadLocale::getLocaleRecord(
                $id,
                LoadLocale::frontendCoreMessageData()['application'],
                LoadLocale::frontendCoreMessageData()['module'],
                LoadLocale::frontendCoreMessageData()['type'],
                'FrontendCoreMessageUpdated',
                'frontend core message value updated'
            )
        );

        $this->assertSame(0, $updatedRows);
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
}
