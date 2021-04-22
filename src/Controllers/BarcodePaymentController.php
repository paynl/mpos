<?php

namespace MPOS\Controllers;

use MPOS\Helpers\ApiHelper;
use MPOS\Exceptions\MPOSException;
use Psr\Http\Message\ServerRequestInterface;
use Laminas\Diactoros\Response;

class BarcodePaymentController extends BaseController
{
    const WECHAT = 'wechat';
    const ALIPAY = 'alipay';
    const GIFTCARD = 'giftcard';

    /** @var ApiHelper */
    private $apiHelper;

    public function __construct(ApiHelper $apiHelper)
    {
        $this->apiHelper = $apiHelper;
    }

    /**
     * @param ServerRequestInterface $request
     * @return Response
     */
    public function checkScannedCard(ServerRequestInterface $request): Response
    {
        $requestBody = (array)$request->getParsedBody();
        $cardNumber = (string)($requestBody['cardNumber'] ?? '');
        $apiToken = (string)$requestBody['apiToken'] ?? '';
        $data = $this->apiHelper->checkScannedCard($cardNumber, $apiToken);

        return $this->response($data);
    }

    /**
     * @param ServerRequestInterface $request
     * @return Response
     * @throws MPOSException
     */
    public function startScannedCardTransaction(ServerRequestInterface $request): Response
    {
        $body = (array)$request->getParsedBody();
        $apiToken = $body['apiToken'] ?? '';
        $cardNumberType = $body['cardNumberType'] ?? '';
        $lang = $this->getLangFromRequest($request);

        if ($cardNumberType == self::WECHAT || $cardNumberType == self::ALIPAY) {
            $parameters = $this->execBarcodePaymentData($body);
            $data = $this->apiHelper->startScannedCardTransaction($parameters, $apiToken);

            return $this->response($data);
        }
        $parameters = $this->execData($body);
        $data = $this->apiHelper->startFundGiftCard($parameters, $apiToken, $lang);

        return $this->response($data);
    }

    /**
     * @param ServerRequestInterface $request
     * @return Response
     */
    public function checkScannedCardTransaction(ServerRequestInterface $request): Response
    {
        $requestBody = (array)$request->getParsedBody();
        $apiToken = (string)($requestBody['apiToken'] ?? '');
        $data = $this->apiHelper->checkScannedCardTransaction($requestBody, $apiToken);

        return $this->response($data);
    }

    /**
     * @param mixed[] $data
     * @return mixed[]
     */
    private function execData(array $data): array
    {
        return [
            'cardNumber' => $data['cardNumber'] ?? '',
            'serviceId' => $data['serviceId'] ?? '',
            'amount' => $data['amount'] ?? '',
            'pin' => $data['pin'] ?? '',
            'description' => $data['description'] ?? '',
            'orderNumber' => $data['orderNumber'] ?? '',
        ];
    }

    /**
     * @param mixed[] $data
     * @return mixed[]
     */
    private function execBarcodePaymentData(array $data): array
    {
        return [
            'scanData' => $data['cardNumber'] ?? '',
            'serviceId' => $data['serviceId'] ?? '',
            'amount' => $data['amount'] ?? '',
            'description' => $data['description'] ?? '',
            'orderNumber' => $data['orderNumber'] ?? '',
        ];
    }
}
