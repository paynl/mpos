<?php

namespace MPOS\Middleware;

use MPOS\Enums\HttpMethodEnum;
use MPOS\Helpers\ConfigHelper;
use MPOS\Exceptions\ConfigException;
use MPOS\Helpers\CsrfHelper;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use \Exception;

class CsrfTokenMiddleware implements MiddlewareInterface
{
    const CSRF_HEADER_KEY = 'x-csrf-token';
    const CSRF_META_TAG_TEMPLATE = '<meta name="csrf-token" content="%s">';
    const CSRF_FIELD = 'csrf_token';
    const CSRF_INPUT_TEMPLATE = '<input type="hidden" name="%s" value="%s">';


    /** @var ConfigHelper */
    private $config;

    /** @var CsrfHelper */
    private $csrfHelper;

    public function __construct(ConfigHelper $config, CsrfHelper $csrfHelper)
    {
        $this->config = $config;
        $this->csrfHelper = $csrfHelper;
    }


    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws ConfigException
     * @throws Exception
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($request->getMethod() != HttpMethodEnum::POST) {
            return $handler->handle($request);
        }
        $tokenLifeTimeConfigValue = (int)$this->config->get('common.csrf.token_lifetime');
        $tokenLifeTimeConfigValue = empty($tokenLifeTimeConfigValue) ? null : $tokenLifeTimeConfigValue * 60;

        // Prior way to pass the token is to add the specific header
        $csrfToken = $this->getCSRFToken($request);

        // tokens are reusable
        $this->csrfHelper->check(CsrfHelper::CSRF_TOKEN_SESSION_NAME, $csrfToken, $tokenLifeTimeConfigValue, true);

        return $handler->handle($request);
    }

    /**
     * @param ServerRequestInterface $request
     * @return string
     */
    private function getCSRFToken(ServerRequestInterface $request): string
    {
        // Prior way to pass the token is to add the specific header
        $csrfToken = $request->getHeaderLine(self::CSRF_HEADER_KEY);

        // But token can be passed as the part of the POST as well
        if (empty($csrfToken)) {
            $requestBody = (array)$request->getParsedBody();
            $csrfToken = (string)($requestBody[self::CSRF_FIELD] ?? '');
        }

        return $csrfToken;
    }
}
