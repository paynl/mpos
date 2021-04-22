<?php

namespace MPOS\Providers;

use Curl\Curl;
use EasyCSRF\NativeSessionProvider;
use Laminas\Http\PhpEnvironment\RemoteAddress;
use MPOS\Helpers\CookieHelper;
use MPOS\Helpers\CsrfHelper;
use MPOS\Helpers\ConfigHelper;
use MPOS\Helpers\CurlHelper;
use MPOS\Helpers\ScriptExecutionSettingsHelper;
use MPOS\Helpers\ApiHelper;
use MPOS\Helpers\TranslationsHelper;
use League\Container\Container;
use League\Container\ServiceProvider\AbstractServiceProvider;
use Symfony\Component\Translation\Translator;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

class HelpersProvider extends AbstractServiceProvider
{
    /** @var string[] */
    protected $provides = [
        ConfigHelper::class,
        ScriptExecutionSettingsHelper::class,
        Run::class,
        PrettyPageHandler::class,
        ApiHelper::class,
        TranslationsHelper::class,
        CsrfHelper::class,
        CookieHelper::class
    ];

    public function register()
    {
        /** @var Container $container */
        $container = $this->getContainer();

        $container->share(ConfigHelper::class, function () {
            return new ConfigHelper();
        });

        /** @var ConfigHelper $config */
        $config = $container->get(ConfigHelper::class);

        $container->share(
            ScriptExecutionSettingsHelper::class,
            function () use ($container, $config) {
                return new ScriptExecutionSettingsHelper(
                    $container->get('request'),
                    $config,
                    $container->get(Run::class),
                    $container->get(PrettyPageHandler::class)
                );
            }
        );

        // Whoops
        $container->share(Run::class, function () {
            return new Run();
        });

        // Whoops Pretty page handler
        $container->share(PrettyPageHandler::class, function () {
            return new PrettyPageHandler();
        });

        $container->share(TranslationsHelper::class, function () use ($container) {
            return new TranslationsHelper(
                $container->get(ConfigHelper::class),
                $container->get(Translator::class)
            );
        });

        $container->share(ApiHelper::class, function () use ($container) {
            return new ApiHelper(
                $container->get(ConfigHelper::class),
                $container->get(CurlHelper::class),
                $container->get(TranslationsHelper::class),
                new RemoteAddress()
            );
        });

        $container->share(CurlHelper::class, function () use ($config) {
            return new CurlHelper(
                new Curl(),
                $config
            );
        });

        $container->share(CsrfHelper::class, function () use ($container) {
            $sessionProvider = $container->get(NativeSessionProvider::class);
            return new CsrfHelper($sessionProvider);
        });

        $container->share(CookieHelper::class, function () use ($container) {
            return new CookieHelper($container->get(ConfigHelper::class));
        });
    }
}
