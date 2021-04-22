<?php

namespace MPOS\Controllers;

use MPOS\Helpers\ApiHelper;
use MPOS\Exceptions\ConfigException;
use MPOS\Exceptions\CurlException;
use Psr\Http\Message\ServerRequestInterface;
use Paynl\QR\Error\Error;
use Laminas\Diactoros\Response;

class QRPaymentController extends BaseController
{
    /** @var ApiHelper */
    private $apiHelper;

    public function __construct(ApiHelper $apiHelper)
    {
        $this->apiHelper = $apiHelper;
    }

    /**
     * @param ServerRequestInterface $request
     * @return Response
     * @throws ConfigException
     * @throws Error
     */
    public function getQRCode(ServerRequestInterface $request): Response
    {
        $result = $this->apiHelper->getQRCode($request);

        return $this->response($result);
    }

    /**
     * @param ServerRequestInterface $request
     * @return Response
     * @throws ConfigException
     * @throws CurlException
     */
    public function getTransactionStatus(ServerRequestInterface $request): Response
    {
        $result = $this->apiHelper->getTransactionStatus($request);

        return $this->response($result);
    }
}
