<?php

namespace Backend\Modules\Locale\Tests\Engine;

use Backend\Modules\Locale\DataFixtures\LoadLocale;
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
}
