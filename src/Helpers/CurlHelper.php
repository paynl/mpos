<?php

namespace MPOS\Helpers;

use Curl\Curl;
use MPOS\Exceptions\CurlException;
use MPOS\Exceptions\ConfigException;

class CurlHelper
{
    private const CURL_TIMEOUT = 10;

    /** @var Curl */
    private $curl;

    /** @var ConfigHelper */
    private $config;

    public function __construct(Curl $curl, ConfigHelper $config)
    {
        $this->curl = $curl;
        $this->config = $config;
    }

    /**
     * @param string $url
     * @param string[] $data
     * @param string|null $token
     * @return string
     * @throws CurlException
     * @throws ConfigException
     */
    public function post(string $url, array $data = [], ?string $token = null): string
    {
        $this->setCurlSettings($token);
        $this->curl->post($url, http_build_query($data));

        return $this->processCurlResponse();
    }


    /**
     * @param string $url
     * @param string[] $data
     * @param string|null $token
     * @return string
     * @throws CurlException
     * @throws ConfigException
     */
    public function get(string $url, array $data = [], ?string $token = null): string
    {
        if (!empty($data)) {
            $url = sprintf('%s?%s', $url, http_build_query($data));
        }
        $this->setCurlSettings($token);
        $this->curl->get($url);

        return $this->processCurlResponse();
    }

    /**
     * @param string|null $token
     * @throws ConfigException
     */
    private function setCurlSettings(?string $token): void
    {
        $this->curl->setTimeout(self::CURL_TIMEOUT);
        $this->curl->setOpt(CURLOPT_RETURNTRANSFER, true);
        $this->curl->setOpt(CURLOPT_PROTOCOLS, CURLPROTO_HTTP | CURLPROTO_HTTPS);
        $tokenCode = $this->config->get('api.auth.token_code');
        if (!is_null($tokenCode) && !is_null($token)) {
            $this->curl->setBasicAuthentication($tokenCode, $token);
        }
    }

    /**
     * @return string
     * @throws CurlException
     */
    private function processCurlResponse(): string
    {
        if ($this->curl->error) {
            throw new CurlException($this->curl->getErrorMessage(), $this->curl->getErrorCode());
        }

        $response = $this->curl->getRawResponse();
        if (empty($response)) {
            throw new CurlException('Empty response received');
        }

        return (string)$response;
    }
}
