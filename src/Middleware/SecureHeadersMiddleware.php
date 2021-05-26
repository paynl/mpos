<?php
namespace MPOS\Middleware;

use Bepsvpt\SecureHeaders\SecureHeaders;
use MPOS\Exceptions\ConfigException;
use MPOS\Helpers\ConfigHelper;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class SecureHeadersMiddleware implements MiddlewareInterface
{
    const SECURE_HEADERS_CONFIG = 'vendor/bepsvpt/secure-headers/config/secure-headers.php';

    const UNWANTED_HEADER_LIST = [
        // Headers that are not secure
        'X-Powered-By',
        'Server',
        // Headers that are unfamiliar for Chrome's desktop version
        'Permissions-Policy'
    ];

    /** @var ConfigHelper */
    private $config;

    public function __construct(ConfigHelper $configHelper)
    {
        $this->config = $configHelper;
    }

    /**
     * Process an incoming server request and return a response, optionally delegating
     * response creation to a handler.
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws ConfigException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->removeUnwantedHeaders(self::UNWANTED_HEADER_LIST);

        $secureHeadersConfig = $this->config->get('common.root_path') . self::SECURE_HEADERS_CONFIG;
        $secureHeaders = SecureHeaders::fromFile($secureHeadersConfig)->headers();
        $response = $handler->handle($request);
        foreach ($secureHeaders as $header => $value) {
            if (in_array($header, self::UNWANTED_HEADER_LIST)) {
                continue;
            }

            $response = $response->withHeader($header, $value);
        }

        return $response;
    }


    /**
     * @param mixed[] $headerList
     */
    private function removeUnwantedHeaders(array $headerList): void
    {
        foreach ($headerList as $header) {
            header_remove($header);
        }
    }
}
