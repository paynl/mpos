<?php

namespace MPOS\Middleware;

use MPOS\Helpers\ConfigHelper;
use MPOS\Exceptions\ConfigException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class SessionMiddleware implements MiddlewareInterface
{
    const LIFETIME = 'lifetime';
    const PATH = 'path';
    const DOMAIN = 'domain';
    const SECURE = 'secure';
    const HTTPONLY = 'httponly';
    const SAMESITE = 'samesite';

    const LAX = 'lax';
    const DEFAULT_COOKIE_LIFE_TIME = 24 * 60 * 60; // 24 hours
    const DEFAULT_SESSION_GC_MAXLIFETIME = 24 * 60 * 60; // 24 hours

    /** @var ConfigHelper */
    private $config;

    public function __construct(ConfigHelper $config)
    {
        $this->config = $config;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws ConfigException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $host = $request->getUri()->getHost();

        $cookieLifeTime = (int)$this->config->get('common.session.cookie_lifetime') * 60;
        if ($cookieLifeTime <= 0) {
            $cookieLifeTime = self::DEFAULT_COOKIE_LIFE_TIME;
        }

        $sessionGcMaxlifetime = (int)$this->config->get('common.session.gc_maxlifetime') * 60;

        // Zero value means that the session will be active before the user's browser is closed
        if ($sessionGcMaxlifetime < 0) {
            $sessionGcMaxlifetime = self::DEFAULT_SESSION_GC_MAXLIFETIME;
        }

        // if you only want to receive the cookie over HTTPS
        $secure = $this->config->get('common.cookie.secure') ? true : false;
        $httpOnly = true; // prevent JavaScript access to session cookie
        $sameSite = self::LAX;

        // Session gc lifetime. Please keep in mind that cron script will take values from the php.ini
        ini_set('session.gc_maxlifetime', (string)$sessionGcMaxlifetime);

        if (PHP_VERSION_ID < 70300) {
            session_set_cookie_params(
                $cookieLifeTime,
                sprintf('/; samesite=%s', $sameSite),
                $host,
                $secure,
                $httpOnly
            );
        } else {
            session_set_cookie_params([
                self::LIFETIME => $cookieLifeTime,
                self::PATH => '/',
                self::DOMAIN => $host,
                self::SECURE => $secure,
                self::HTTPONLY => $httpOnly,
                self::SAMESITE => $sameSite
            ]);
        }

        session_start();

        return $handler->handle($request);
    }
}
