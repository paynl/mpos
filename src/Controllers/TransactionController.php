<?php

namespace MPOS\Controllers;

use MPOS\Helpers\ApiHelper;
use Psr\Http\Message\ServerRequestInterface;
use Laminas\Diactoros\Response;

class TransactionController extends BaseController
{
    /** @var ApiHelper */
    private $apiHelper;

    public function __construct(ApiHelper $apiHelper)
    {
        $this->apiHelper = $apiHelper;
    }

    public function approve(ServerRequestInterface $request): Response
    {
        $lang = $this->getLangFromRequest($request);

        return $this->apiHelper->approveTransaction($request, $lang);
    }

    public function decline(ServerRequestInterface $request): Response
    {
        $lang = $this->getLangFromRequest($request);

        return $this->apiHelper->declineTransaction($request, $lang);
    }

    public function cancel(ServerRequestInterface $request): Response
    {
        $lang = $this->getLangFromRequest($request);

        return $this->apiHelper->cancelTransaction($request, $lang);
    }
}
