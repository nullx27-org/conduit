<?php

namespace Conduit\Tests;

use Conduit\Authentication;
use PHPUnit\Framework\TestCase;

class AuthenticationTest extends TestCase
{

    protected $authentication;

    public function setUp()
    {
        $this->authentication = new Authentication($_ENV['client_id'], $_ENV['client_secret'], $_ENV['refresh_token']);
    }

    public function testGetAccessToken()
    {
        $token = $this->authentication->getAccessToken();

        $this->assertNotNull($token);
    }
}
