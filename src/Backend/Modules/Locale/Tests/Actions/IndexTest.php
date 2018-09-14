<?php

namespace Backend\Modules\Locale\Tests\Action;

use Backend\Modules\Locale\DataFixtures\LoadLocale;
use Common\WebTestCase;

class IndexTest extends WebTestCase
{
    public function testAuthenticationIsNeeded(): void
    {
        $client = static::createClient();
        $this->logout($client);
        $this->loadFixtures(
            $client,
            [
                'Backend\Modules\Locale\DataFixtures\LoadLocale'
            ]
        );

        $client->setMaxRedirects(1);
        $client->request('GET', '/private/en/locale/index');

        // we should get redirected to authentication with a reference to blog index in our url
        self::assertStringEndsWith(
            '/private/en/authentication?querystring=%2Fprivate%2Fen%2Flocale%2Findex',
            $client->getHistory()->current()->getUri()
        );
    }

    public function testIndexFilter(): void
    {
        $client = static::createClient();
        $this->login($client);

        $crawler = $client->request('GET', '/private/en/locale/index');

        self::assertContains(
            'Update filter',
            $client->getResponse()->getContent()
        );

        // select the form and fill in some values
        $form = $crawler->selectButton('Update filter')->form();
        $form['name'] = LoadLocale::backendCoreLabelData()['name'];

        // submits the given form
        $client->submit($form);
        self::assertEquals(200, $client->getResponse()->getStatusCode());

        $form['value'] = LoadLocale::backendCoreLabelData()['value'];

        // submits the given form
        $client->submit($form);
        self::assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
