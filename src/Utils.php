<?php
declare(strict_types=1);

namespace Harpya\SDK;

use \Harpya\SDK\Constants;

/**
 *
 */
class Utils
{
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

    /**
     *
     */
    public static function getSIDFromSessionToken($remoteSessionToken = false)
    {
        if (!$remoteSessionToken) {
            throw new \Exception('Invalid session token');
        }
        $sessionID = substr($remoteSessionToken, strpos($remoteSessionToken, ':') + 1);
        return $sessionID;
    }
}
