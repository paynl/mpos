<?php

namespace MPOS\Controllers;

use MPOS\Enums\LanguagesEnum;
use MPOS\Exceptions\ConfigException;
use MPOS\Helpers\ConfigHelper;
use League\Route\ContainerAwareInterface;
use League\Route\ContainerAwareTrait;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Twig\Environment as TwigEnvironment;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\Response\RedirectResponse;
use UnexpectedValueException;

class BaseController implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    const LANG = 'lang';

    /**
     * @param string $value
     * @return mixed
     */
    protected function get(string $value)
    {
        /** @var ContainerInterface $container */
        $container = $this->getContainer();

        return $container->get($value);
    }

    /**
     * @param string $value
     * @return mixed
     * @throws ConfigException
     */
    protected function getConfig(string $value)
    {
        /** @var ConfigHelper $configHelper */
        $configHelper = $this->get(ConfigHelper::class);

        return $configHelper->get($value);
    }

    protected function getTwig(): TwigEnvironment
    {
        return $this->get(TwigEnvironment::class);
    }

    protected function redirect(string $url) : Response
    {
        return new RedirectResponse($url);
    }

    protected function response(string $content = ''): Response
    {
        $response = new Response();
        $response->getBody()->write($content);

        return $response;
    }

    protected function getLangFromRequest(ServerRequestInterface $request): string
    {
        $cookieParams = $request->getCookieParams();
        $languageCode = $cookieParams[self::LANG] ?? LanguagesEnum::DEFAULT_LANG;

        try {
            $languagesEnum = new LanguagesEnum($languageCode);

            return $languagesEnum->getValue();
        } catch (UnexpectedValueException $exception) {
            return LanguagesEnum::DEFAULT_LANG;
        }
    }
}
