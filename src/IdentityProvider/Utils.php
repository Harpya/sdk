<?php
declare(strict_types=1);

namespace Harpya\SDK\IdentityProvider;

use \Harpya\SDK\Constants;

/**
 *
 */
class Utils
{
    /**
     * Prepare the associative array with correct values to communicate with Identity Provider
     */
    public static function getInitialAuthRequestEnvelope() : array
    {
        $pack = [
            // Config section
            Constants::KEY_AUTHORIZE => getenv(Constants::CONFIG_APPLICATION_BASE_URL) . getenv(Constants::CONFIG_APPLICATION_AUTHORIZATION_PATH),
            Constants::KEY_APPLICATION_ID => getenv(Constants::CONFIG_APPLICATION_ID),
            Constants::KEY_APPLICATION_SECRET => getenv(Constants::CONFIG_APPLICATION_SECRET),

            // Current request
            Constants::KEY_CLIENT_IP => $_SERVER['REMOTE_ADDR']
        ];

        // If have data from session: probably because the session is expired
        if (isset($_SESSION['auth']) && is_array($_SESSION['auth']) && isset($_SESSION['token'])) {
            $pack[Constants::KEY_TOKEN] = $_SESSION['auth'][Constants::KEY_TOKEN];
        }

        return $pack;
    }

    /**
     *
     */
    public static function checkIfAuthSessionIsValid($authData = [])
    {
        $valid = false;

        if (empty($authData)) {
            return false;
        }

        if (!is_array($authData)) {
            return false;
        }

        if (isset($authData['ttl']) && (time() < $authData['ttl'])
        // && ($auth['ip'] == $_SERVER['REMOTE_ADDR'])
        ) {
            $valid = true;
        }

        return $valid;
    }

    /**
     *
     */
    public static function isSessionStarted()
    {
        if (version_compare(phpversion(), '5.4.0', '>=')) {
            return session_status() === PHP_SESSION_ACTIVE ? true : false;
        } else {
            return session_id() === '' ? false : true;
        }
        return false;
    }

    /**
     *
     */
    public static function encodeToken($s)
    {
        return \base64_encode($s);
    }

    /**
     *
     */
    public static function decodeToken($s)
    {
        return \base64_decode($s);
    }

    /**
     *
     */
    public static function generateRandomToken() : string
    {
        $prefix = \getenv(Constants::CONFIG_PREFIX_TOKEN) ?? 'harpya';
        $salt = \getenv(Constants::CONFIG_SALT_TOKEN) ?? '';
        $token = $prefix . ':' . hash('sha256', $salt . time() . \random_bytes(20));
        return $token;
    }

    /**
     * Simply checks if last character of a given URL is '/'.
     * If not, just add it.
     */
    public static function addSlashAtEnd(&$url) : void
    {
        if (!$url || (is_string($url) && substr($url, -1) !== '/')) {
            $url .= '/';
        }
    }

    /**
     * Calculate the TTL (time to live) in seconds. It is used to set
     * the session lifetime.
     */
    public static function getTTL($minutes = 1440) : int
    {
        $ttl = intval(getenv('CONFIG_SESSION_TTL'));
        if (!$ttl) {
            $ttl = $minutes; // 1 day
        }
        return $ttl * 60; // converts to seconds
    }
}
