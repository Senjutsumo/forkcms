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

    public function testConstantTypes():void
    {
        $typeArray = ['act', 'err', 'lbl', 'msg'];
        $this->assertSame($typeArray, Model::TYPES);

        $typeArray = ['act', 'test', 'lbl', 'msg'];
        $this->assertNotSame($typeArray, Model::TYPES);
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

        $this->assertFalse(
            Model::existsByName(
                'TestWrongLabel',
                'act',
                'Core',
                'en',
                'Frontend',
                LoadLocale::backendCoreActionData()['id']
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
            9001,
            Model::getByName(
                LoadLocale::backendCoreErrorData()['name'],
                LoadLocale::backendCoreErrorData()['type'],
                LoadLocale::backendCoreErrorData()['module'],
                LoadLocale::backendCoreErrorData()['language'],
                LoadLocale::backendCoreErrorData()['application']
            )
        );

        $this->assertNotSame(
            9002,
            Model::getByName(
                LoadLocale::backendCoreErrorData()['name'],
                LoadLocale::backendCoreErrorData()['type'],
                LoadLocale::backendCoreErrorData()['module'],
                LoadLocale::backendCoreErrorData()['language'],
                LoadLocale::backendCoreErrorData()['application']
            )
        );
    }
}
