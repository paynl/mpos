<?php

namespace MPOS\Providers;

use EasyCSRF\NativeSessionProvider;
use MPOS\Enums\LanguagesEnum;
use MPOS\Helpers\ConfigHelper;
use MPOS\Helpers\CsrfHelper;
use MPOS\Helpers\TranslationsHelper;
use League\Container\Container;
use League\Container\ServiceProvider\AbstractServiceProvider;
use MPOS\Middleware\CsrfTokenMiddleware;
use Symfony\Component\Translation\Loader\ArrayLoader;
use Symfony\Component\Translation\Translator;
use Twig\Environment as TwigEnvironment;
use Twig\Extension\DebugExtension;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

class ExternalHelpersProvider extends AbstractServiceProvider
{
    const FORMAT_ARRAY = 'array';

    /** @var string[] */
    protected $provides = [
        TwigEnvironment::class,
        Translator::class,
        NativeSessionProvider::class
    ];

    public function register()
    {
        /** @var Container $container */
        $container = $this->getContainer();

        /** @var ConfigHelper $config */
        $config = $container->get(ConfigHelper::class);

        $container->share(Translator::class, function () {
            $translationNl = require_once(__DIR__ . '/../../lang/nl.php');
            $translationEn = require_once(__DIR__ . '/../../lang/en.php');

            $nlLocale = LanguagesEnum::getLocaleByLanguage(LanguagesEnum::DEFAULT_LANG);
            $enLocale = LanguagesEnum::getLocaleByLanguage(LanguagesEnum::LANG_EN);

            $translator = new Translator($nlLocale);
            $translator->addLoader(self::FORMAT_ARRAY, new ArrayLoader());
            $translator->addResource(self::FORMAT_ARRAY, $translationNl, $nlLocale);
            $translator->addResource(self::FORMAT_ARRAY, $translationEn, $enLocale);

            return $translator;
        });

        $container->share(NativeSessionProvider::class, function () {
            return new NativeSessionProvider();
        });

        $container->share(TwigEnvironment::class, function () use ($config, $container) {
            $pathToViews = $config->get('common.views_path');
            $loader = new FilesystemLoader($pathToViews);
            $twig = new TwigEnvironment($loader, ['debug' => false]);
            $twig->addExtension(new DebugExtension());

            $translate = new TwigFunction(
                'translate',
                function (?string $lang, string $messageKey) use ($container) {
                    $translateHelper = $container->get(TranslationsHelper::class);

                    return $translateHelper->translate($lang, $messageKey);
                }
            );
            $twig->addFunction($translate);

            /** @var CsrfHelper $csrfHelper */
            $csrfHelper = $container->get(CsrfHelper::class);
            // Use token per session
            $currentCsrfToken = $csrfHelper->getCurrentToken(CsrfHelper::CSRF_TOKEN_SESSION_NAME);
            $csrfToken = $currentCsrfToken ?? $csrfHelper->generate(CsrfHelper::CSRF_TOKEN_SESSION_NAME);

            $csrfHeaderFunction = new TwigFunction(
                'csrf_header',
                function () use ($csrfToken) {
                    return sprintf(CsrfTokenMiddleware::CSRF_META_TAG_TEMPLATE, $csrfToken);
                },
                ['is_safe' => ['html']]
            );
            $twig->addFunction($csrfHeaderFunction);

            $csrfInputFunction = new TwigFunction(
                'csrf_input',
                function () use ($csrfToken) {
                    return sprintf(
                        CsrfTokenMiddleware::CSRF_INPUT_TEMPLATE,
                        CsrfTokenMiddleware::CSRF_FIELD,
                        $csrfToken
                    );
                },
                ['is_safe' => ['html']]
            );
            $twig->addFunction($csrfInputFunction);

            return $twig;
        });
    }
}
