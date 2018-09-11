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
}
