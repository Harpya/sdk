<?php
declare(strict_types=1);

namespace Harpya\SDK\IdentityProvider;

use \Harpya\SDK\IdentityProvider\Utils;
use \Harpya\SDK\Constants;

/**
 *
 */
class Broker
{
    const ACTION_ERROR = 'error';
    const ACTION_REDIRECT = 'redirect';
    const ACTION_REFRESH = 'refresh';

    protected $client;

    /**
     *
     */
    public function getClient()
    {
        if (!$this->client) {
            $this->client = new \GuzzleHttp\Client();
        }

        return $this->client;
    }

    /**
     *
     */
    public function authenticate()
    {
        $resp1stStep = $this->sendAuthRequest();

        if (is_array($resp1stStep)) {
            $action = $resp1stStep[Constants::KEY_ACTION] ?? static::ACTION_ERROR;
            switch ($action) {
                case static::ACTION_REFRESH: // assume the token
                    $this->refreshAuthSession($resp1stStep);
                break;
                case static::ACTION_REDIRECT:
                    $this->redirectLoginPage($resp1stStep);
                break;
                case static::ACTION_ERROR:
                    default:
                    $this->showErrorPage($resp1stStep);
                break;
            }
        }
    }

    /**
     *
     */
    public function refreshAuthSession(array $identityProviderResponse)
    {
        $sessionID = $identityProviderResponse['session_id'];

        $lifetime = time() + 60;

        $authData = [
            Constants::KEY_TOKEN => $identityProviderResponse[Constants::KEY_TOKEN],
            'client_ip' => $identityProviderResponse['client_ip'],
            Constants::KEY_USER_EMAIL => $identityProviderResponse[Constants::KEY_USER_EMAIL],
            'ttl' => $lifetime, // 5 minutes
        ];

        if (session_id() === '') {
            @session_id($sessionID);
            @session_start();
            @setcookie(session_name(), session_id(), $lifetime);
        }

        $_SESSION['auth'] = $authData;
    }

    /**
     *
     */
    public function showErrorPage(array $identityProviderResponse)
    {
        http_response_code(400);
        header('Content-type: application/json');
        echo \json_encode($identityProviderResponse);
        exit;
    }

    /**
     *
     */
    public function redirectLoginPage(array $identityProviderResponse)
    {
        http_response_code(302);

        $code = $identityProviderResponse[Constants::KEY_TOKEN];

        // header('X-Identity-Provider-Identification: A0D47F');
        // header('X-Identity-Provider-Token: abcdef0123456789');
        // header('X-Identity-Provider-Webhook: http://localhost:1991/auth');
        // header('X-Identity-Provider-Callback: http://localhost:1991/');
        header('Location: ' . getenv(Constants::CONFIG_IDENTITY_PROVIDER_EXTERNAL_URL) . '/login/' . $code);
        exit;
        return;
    }

    /**
     *
     */
    public function sendAuthRequest()
    {
        $pack = Utils::getInitialAuthRequestEnvelope();

        $url = getenv(Constants::CONFIG_IDENTITY_PROVIDER_INTERNAL_URL) . '/api/v1/auth_request';

        try {
            $appReturn = $this->getClient()->request('POST', $url, [
                'form_params' => $pack
            ]);
        } catch (\Exception $e) {
            echo '<pre>';
            echo $e->getMessage();
            echo "\n " . $e->getTraceAsString();
            exit;
        }
        $jsonReturn = $appReturn->getBody()->getContents();
        $arrReturn = json_decode($jsonReturn, true);

        return $arrReturn;
    }

    /**
     *
     */
    public function getAuthData($tokenID)
    {
        $pack = Utils::getInitialAuthRequestEnvelope();

        $nm = Constants::KEY_SESSION_ID;
        $pack[Constants::KEY_TOKEN] = $tokenID;

        $url = getenv(Constants::CONFIG_IDENTITY_PROVIDER_INTERNAL_URL) . '/api/v1/auth_confirm';
        try {
            $appReturn = $this->getClient()->request('POST', $url, [
                'form_params' => $pack
            ]);
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $rawMsg = $e->getResponse()->getBody()->getContents();
            $arrMsg = json_decode($rawMsg, true);
            if (is_array($arrMsg) && isset($arrMsg['msg'])) {
                throw new \Exception($arrMsg['msg'], $e->getCode());
            } else {
                throw new \Exception($e->getMessage(), $e->getCode());
            }
        } catch (\Exception $e) {
            throw new \Exception(get_class($e) . ' :: ' . $e->getMessage(), $e->getCode());
            // echo '<pre>';
            // echo $e->getMessage();
            // echo "\n " . $e->getTraceAsString();
            // exit;
        }
        $jsonReturn = $appReturn->getBody()->getContents();
        $arrReturn = json_decode($jsonReturn, true);

        // echo '<pre>';
        // print_r($pack);
        // print_r($arrReturn);
        // exit;

        return $arrReturn;
    }
}
