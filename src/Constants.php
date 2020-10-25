<?php
declare(strict_types=1);

namespace Harpya\SDK;

/**
 *
 */
class Constants
{
    const CONFIG_APPLICATION_ID = 'HSDK_APP_ID';
    const CONFIG_APPLICATION_SECRET = 'HSDK_APP_SECRET';
    const CONFIG_APPLICATION_BASE_URL = 'HSDK_APP_BASE_URL';
    const CONFIG_APPLICATION_AUTHORIZATION_PATH = 'HSDK_APP_AUTH_PATH';
    const CONFIG_IDENTITY_PROVIDER_EXTERNAL_URL = 'HSDK_IP_EXTERNAL_URL';
    const CONFIG_IDENTITY_PROVIDER_INTERNAL_URL = 'HSDK_IP_INTERNAL_URL';

    const CONFIG_HIP_HOSTNAME = 'HIP_HOSTNAME';
    const CONFIG_HIP_DEFAULT_URL = 'HIP_DEFAULT_URL';

    const CONFIG_PREFIX_TOKEN = 'HSDK_PREFIX_TOKEN';
    const CONFIG_SALT_TOKEN = 'HSDK_SALT_TOKEN';

    const CONFIG_IP_SESSION_TTL = 'HSDK_SESSION_TTL';
    const CONFIG_DATETIME_FORMAT = 'HSDK_DATETIME_FORMAT';

    // Informs to IP which URL should be called after authorized
    const KEY_AUTHORIZE = 'authorize';

    // Application's identitfier
    const KEY_APPLICATION_ID = 'application_id';

    // Application's key to validate it's authenticity
    const KEY_APPLICATION_SECRET = 'application_secret';

    // Browser IP
    const KEY_CLIENT_IP = 'client_ip';

    const KEY_SESSION_ID = 'session_id';
    const KEY_TOKEN = 'token';
    const KEY_USER_EMAIL = 'email';
    const KEY_ACTION = 'action';
    const KEY_BASE_URL = 'base_url';
    const KEY_URL_AUTHORIZE = 'url_authorize';
    const KEY_URL_AFTER_LOGIN = 'url_after_login';

    const RESPONSE_PROCEED_VIEW_PROCESSING = -100;
}
