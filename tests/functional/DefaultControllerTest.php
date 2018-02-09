<?php

namespace Tests\Functional;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class DefaultControllerTest extends WebTestCase
{
    /**
     * @param $url
     * @dataProvider getPublicUrls()
     */
    public function testPublicUrls($url)
    {
        $client = static::createClient();
        $client->request('GET', $url);
//        echo $client->getResponse()->getContent();die();
        $this->assertSame(
            Response::HTTP_OK,
            $client->getResponse()->getStatusCode());
    }

    public function getPublicUrls()
    {
        yield ['/'];
//        yield ['/login'];
    }
}
