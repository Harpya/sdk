<?php

use PHPUnit\Framework\TestCase;
use Harpya\SDK\IdentityProvider\Broker;
use Harpya\SDK\IdentityProvider\Utils;
use Harpya\SDK\Constants;

class BrokerTest extends TestCase
{
    public function getBroker()
    {
        $broker = new class extends Broker {
            public function getClient()
            {
                if (!$this->client) {
                    $this->client = new class {
                        public $method;
                        public $url;
                        public $options = [];

                        public $requestResponse = [];

                        public function request($method = '', $url = '', $options = [])
                        {
                            $this->method = $method;
                            $this->url = $url;
                            $this->options = $options;

                            $response = new class($this->requestResponse) {
                                public $body;

                                public function __construct($response = [])
                                {
                                    $this->body = new class {
                                        public $r = [];

                                        public function getContents()
                                        {
                                            return json_encode($this->r);
                                        }
                                    };
                                    $this->body->r = $response;
                                }

                                public function getBody()
                                {
                                    return $this->body;
                                }
                            };

                            //$response->r->

                            return $response;
                        }
                    };
                }

                return $this->client;
            }
        };
        return $broker;
    }

    /**
     * Simple test to check if returns a valid Client object;
     */
    public function testGetClient()
    {
        $broker = new Broker();

        $client = $broker->getClient();

        $this->assertTrue(is_object($client));
    }

    /**
     * Simulate send a valid auth request
     */
    public function testSendAuthRequest()
    {
        $externalURL = 'http://my_host:1015';
        $expectedResponseToken = 'abcdef';

        putenv(Constants::CONFIG_IDENTITY_PROVIDER_INTERNAL_URL . '=' . $externalURL);
        putenv(Constants::CONFIG_IDENTITY_PROVIDER_EXTERNAL_URL . '=' . $externalURL);

        $broker = $this->getBroker();

        $broker->getClient()->requestResponse = [
            'success' => true,
            'token' => $expectedResponseToken
        ];

        $response = $broker->sendAuthRequest();

        $this->assertArrayHasKey('success', $response);
        $this->assertArrayHasKey('token', $response);
        $this->assertTrue($response['success']);
        $this->assertEquals($expectedResponseToken, $response['token']);
        $this->assertEquals($externalURL . '/api/v1/auth_request', $broker->getClient()->url);
        $this->assertEquals('POST', $broker->getClient()->method);
    }

    public function testRefreshAuthSession()
    {
        if (Utils::isSessionStarted()) {
            session_destroy();
        }

        $data = [
            Constants::KEY_SESSION_ID => 'abcdef',
            Constants::KEY_USER_EMAIL => 'email@domain.com',
            Constants::KEY_TOKEN => 'abcdef1234567890',
        ];

        $broker = $this->getBroker();
        $broker->refreshAuthSession($data);

        $this->assertEquals($data[Constants::KEY_TOKEN], $_SESSION['auth'][Constants::KEY_TOKEN]);
        $this->assertEquals($data[Constants::KEY_USER_EMAIL], $_SESSION['auth'][Constants::KEY_USER_EMAIL]);
    }
}
