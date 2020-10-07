<?php

use PHPUnit\Framework\TestCase;
use \Harpya\SDK\IdentityProvider\Utils;
use \Harpya\SDK\Constants;

class UtilsTest extends TestCase
{
    /** @test */
    public function testGetInitialAuthRequestEnvelopeWithSuccess()
    {
        // CONFIG_APPLICATION_ID
        putenv(Constants::CONFIG_APPLICATION_ID . '=abcdef0123456789');
        putenv(Constants::CONFIG_APPLICATION_SECRET . '=abcdef012345678901234567890abcdef1234567890');
        putenv(Constants::CONFIG_APPLICATION_BASE_URL . '=http://localhost:8080');
        putenv(Constants::CONFIG_APPLICATION_AUTHORIZATION_PATH . '=/authorize');

        $_SERVER['REMOTE_ADDR'] = '10.0.0.1';

        $pack = Utils::getInitialAuthRequestEnvelope();

        $this->assertTrue(is_array($pack));
        $this->assertArrayHasKey(Constants::KEY_AUTHORIZE, $pack);
        $this->assertArrayHasKey(Constants::KEY_APPLICATION_ID, $pack);
        $this->assertArrayHasKey(Constants::KEY_APPLICATION_SECRET, $pack);
        $this->assertArrayHasKey(Constants::KEY_CLIENT_IP, $pack);

        $this->assertEquals(getenv(Constants::CONFIG_APPLICATION_BASE_URL) . getenv(Constants::CONFIG_APPLICATION_AUTHORIZATION_PATH), $pack[Constants::KEY_AUTHORIZE]);
        $this->assertEquals(getenv(Constants::CONFIG_APPLICATION_ID), $pack[Constants::KEY_APPLICATION_ID]);
        $this->assertEquals(getenv(Constants::CONFIG_APPLICATION_SECRET), $pack[Constants::KEY_APPLICATION_SECRET]);
        $this->assertEquals($_SERVER['REMOTE_ADDR'], $pack[Constants::KEY_CLIENT_IP]);
    }

    public function dataIfIsValidSession()
    {
        return [
            [null, false],
            ['something', false],
            [[], false],
            [['ttl' => time() - 10], false],
            [['ttl' => time() + 60], true],
        ];
    }

    /**
     * @dataProvider dataIfIsValidSession
     */
    public function testIfIsValidSession($authData, $isValid)
    {
        $this->assertEquals($isValid, Utils::checkIfAuthSessionIsValid($authData));
    }
}
