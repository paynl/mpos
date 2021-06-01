<?php

namespace MPOS\Helpers;

use MPOS\Exceptions\ConfigException;

class CookieHelper
{
    // Default lifetime is 3 month
    const DEFAULT_COOKIE_LIFETIME = 3 * 30 * 24 * 60;
    const ENCRYPTION_MODE = 'AES-256-ECB';
    const COOKIE_PATH = '/';

    /** @var ConfigHelper */
    private $configHelper;

    /** @var string */
    private $host = '';

    /** @var string[] */
    private $cookies = [];

    public function __construct(ConfigHelper $configHelper)
    {
        $this->configHelper = $configHelper;
    }

    public function setHost(string $host): void
    {
        $this->host = $host;
    }

    /**
     * @param string[] $cookies
     */
    public function setCookies(array $cookies): void
    {
        $this->cookies = $cookies;
    }

    /**
     * @param string $cookieName
     * @param mixed $value
     * @param bool $encryptValue
     * @throws ConfigException
     */
    public function saveCookieValue(string $cookieName, $value, bool $encryptValue = false): void
    {
        $cookieLifetime = (int)$this->configHelper->get('common.cookie.lifetime') * 60;
        if (empty($cookieLifetime)) {
            $cookieLifetime = self::DEFAULT_COOKIE_LIFETIME * 60;
        }
        $expiry = time() + $cookieLifetime;
        $secure = $this->configHelper->get('common.cookie.secure') ? true : false;
        $httpOnly = true; // prevent JavaScript access to session cookie

        $key = $this->configHelper->get('common.cookie.encryption_key');
        if (!empty($value) && $encryptValue) {
            $encryptedValue = openssl_encrypt($value, self::ENCRYPTION_MODE, $key);
            // Keep the value empty if it was not possible to encrypt
            $value = $encryptedValue ? base64_encode($encryptedValue) : '';
        }

        setcookie($cookieName, $value, $expiry, self::COOKIE_PATH, $this->host, $secure, $httpOnly);
    }

    /**
     * @param string $httpCookieString
     * @param string[] $cookiesToDelete
     */
    public function deleteCookies(string $httpCookieString, array $cookiesToDelete): void
    {
        $cookies = explode(';', $httpCookieString);
        foreach ($cookies as $cookie) {
            $parts = explode('=', $cookie);
            $name = trim($parts[0]);
            if (in_array($name, $cookiesToDelete)) {
                setcookie($name, '', time() - 3600);
                setcookie($name, '', time() - 3600, self::COOKIE_PATH);
                setcookie($name, '', time() - 3600, self::COOKIE_PATH, $this->host);
            }
        }
    }

    /**
     * @param string $cookieName
     * @param bool $secure
     * @return string
     * @throws ConfigException
     */
    public function getCookieValue(string $cookieName, bool $secure = false): string
    {
        $value = $this->cookies[$cookieName] ?? '';

        $key = $this->configHelper->get('common.cookie.encryption_key');
        if (!empty($value) && $secure) {
            $value = openssl_decrypt(base64_decode($value), self::ENCRYPTION_MODE, $key);
        }

        return (string)$value;
    }
}
