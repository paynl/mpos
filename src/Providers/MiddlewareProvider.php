<?php

namespace MPOS\Providers;

use League\Container\ServiceProvider\AbstractServiceProvider;
use League\Container\Container;
use MPOS\Helpers\ConfigHelper;
use MPOS\Helpers\CsrfHelper;
use MPOS\Middleware\CsrfTokenMiddleware;
use MPOS\Middleware\SecureHeadersMiddleware;
use MPOS\Middleware\SessionMiddleware;

class MiddlewareProvider extends AbstractServiceProvider
{
    /** @var string[] */
    protected $provides = [
        SecureHeadersMiddleware::class,
        SessionMiddleware::class,
        CsrfTokenMiddleware::class
    ];

    public function register()
    {
        /** @var Container $container */
        $container = $this->getContainer();
        $config = $container->get(ConfigHelper::class);
        $container->share(SecureHeadersMiddleware::class, function () use ($config) {
            return new SecureHeadersMiddleware($config);
        });

        $container->share(SessionMiddleware::class, function () use ($config) {
            return new SessionMiddleware($config);
        });

        $container->share(CsrfTokenMiddleware::class, function () use ($config, $container) {
            $csrfHelper = $container->get(CsrfHelper::class);

            return new CsrfTokenMiddleware($config, $csrfHelper);
        });
    }
}
