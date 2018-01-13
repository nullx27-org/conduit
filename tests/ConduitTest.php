<?php

namespace Conduit\Tests;

use Conduit\Authentication;
use Conduit\Conduit;
use Conduit\Response;
use Kevinrob\GuzzleCache\CacheMiddleware;
use PHPUnit\Framework\TestCase;

class ConduitTest extends TestCase
{
    public function testNonAuthRequest()
    {
        $api = new Conduit();
        $response = $api->alliances(99006112)->get();

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals('Friendly Probes', $response->name);
    }

    public function testAuthRequest()
    {
        $auth = new Authentication($_ENV['client_id'], $_ENV['client_secret'], $_ENV['refresh_token']);;
        $api = new Conduit($auth);

        $response = $api->characters(95149868)->clones()->get();

        $this->assertNotNull($response->last_clone_jump_date);
        $this->assertNotNull($response->last_station_change_date);
    }

    public function testCachedRequest()
    {
        $api = new Conduit();

        $response = $api->corporations(98224068)->get();
        $this->assertEquals(CacheMiddleware::HEADER_CACHE_MISS, $response->getHeader(CacheMiddleware::HEADER_CACHE_INFO));

        $response = $api->corporations(98224068)->get();
        $this->assertEquals(CacheMiddleware::HEADER_CACHE_HIT, $response->getHeader(CacheMiddleware::HEADER_CACHE_INFO));
    }

}
