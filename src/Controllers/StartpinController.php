<?php

namespace MPOS\Controllers;

use MPOS\Helpers\ApiHelper;
use MPOS\Exceptions\MPOSException;
use Psr\Http\Message\ServerRequestInterface;
use Laminas\Diactoros\Response;

class StartpinController extends BaseController
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
     * @throws MPOSException
     */
    public function doInstoreStart(ServerRequestInterface $request): Response
    {
        $lang = $this->getLangFromRequest($request);
        $apiResponse = $this->apiHelper->doInstoreStart($request, $lang);

        return $this->response($apiResponse);
    }

    /**
     * @param ServerRequestInterface $request
     * @return Response
     * @throws MPOSException
     */
    public function doInstorePayment(ServerRequestInterface $request): Response
    {
        $lang = $this->getLangFromRequest($request);
        $apiResponse = $this->apiHelper->doInstorePayment($request, $lang);

        return $this->response($apiResponse);
    }

    /**
     * @param ServerRequestInterface $request
     * @return Response
     * @throws MPOSException
     */
    public function confirmPayment(ServerRequestInterface $request): Response
    {
        $lang = $this->getLangFromRequest($request);

        return $this->apiHelper->confirmPayment($request, $lang);
    }
}
