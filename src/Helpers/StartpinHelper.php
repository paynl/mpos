<?php

namespace MPOS\Helpers;

use Curl\Curl;
use ErrorException;
use Laminas\Http\PhpEnvironment\RemoteAddress;
use MPOS\Enums\PayAPIFieldEnum;
use MPOS\Enums\PayAPIResultEnum;
use MPOS\Exceptions\ConfigException;
use MPOS\Exceptions\CurlException;
use MPOS\Exceptions\FormValidationException;
use MPOS\Exceptions\GiftCardValidationException;
use MPOS\Exceptions\MPOSException;
use MPOS\ValueObjects\StartpinConfirmPaymentValueObject;
use MPOS\ValueObjects\StartpinPaymentValueObject;
use MPOS\ValueObjects\StartpinStartValueObject;
use MPOS\ValueObjects\TransactionValueObject;
use Paynl\Config;
use Paynl\Error\Error;
use Paynl\QR\UUID;
use Paynl\Transaction;
use Paynl\QR\Error\Error as QrError;
use Psr\Http\Message\ServerRequestInterface;
use Laminas\Diactoros\Response;
use Laminas\Diactoros\Response\JsonResponse;

class StartpinHelper
{
    const STARTPIN_FORM_GROUP = 'startpin_form';
    const API_ERROR_PAY_0 = 'api_response:api_error_PAY_0';

    /** @var ConfigHelper */
    private $config;

    /** @var CurlHelper */
    private $curlHelper;

    /** @var TranslationsHelper */
    private $translationsHelper;

    /** @var RemoteAddress */
    private $remoteAddress;

    public function __construct(
        ConfigHelper $config,
        CurlHelper $curlHelper,
        TranslationsHelper $translationsHelper,
        RemoteAddress $remoteAddress
    ) {
        $this->config = $config;
        $this->curlHelper = $curlHelper;
        $this->translationsHelper = $translationsHelper;
        $this->remoteAddress = $remoteAddress;
    }

    /**
     * @param ServerRequestInterface $request
     * @param string $lang
     * @return string
     * @throws ConfigException
     * @throws MPOSException
     */
    public function getInstoreServices(ServerRequestInterface $request, string $lang): string
    {
        $requestBody = (array)$request->getParsedBody();
        $postedToken = (string)($requestBody['token'] ?? '');

        return $this->getInstoreServicesByTokenAndLang($postedToken, $lang);
    }

    /**
     * @param string $token
     * @param string $lang
     * @return string
     * @throws ConfigException
     * @throws MPOSException
     */
    public function getInstoreServicesByTokenAndLang(string $token, string $lang): string
    {
        if (empty($token)) {
            return $this->jsonData([
                'request' => [
                    'result' => 0,
                    'errorId' => '',
                    'errorMessage' => $this->getTranslation('error:invalid_token', $lang)
                ]
            ]);
        }

        try {
            $getServicesApiUrl = $this->config->get('api.api.startpin.getServices');
            return $this->curlHelper->post($getServicesApiUrl, [], $token);
        } catch (CurlException $exception) {
            return $this->jsonData([
                'request' => [
                    'result' => 0,
                    'errorId' => '',
                    'errorMessage' => $this->getTranslation('error:could_not_get_services', $lang)
                ]
            ]);
        }
    }

    /**
     * @param ServerRequestInterface $request
     * @param string $lang
     * @return string
     * @throws ConfigException
     * @throws MPOSException
     */
    public function getInstoreTerminals(ServerRequestInterface $request, string $lang): string
    {
        $requestBody = (array)$request->getParsedBody();
        $postedToken = (string)($requestBody['token'] ?? '');

        return $this->getInstoreTerminalsByTokenAndLang($postedToken, $lang);
    }

    /**
     * @param ServerRequestInterface $request
     * @return string
     * @throws ConfigException
     * @throws CurlException
     */
    public function getTransactionStatus(ServerRequestInterface $request): string
    {
        $body = (array)$request->getParsedBody();
        $apiToken = $body['api_token'] ?? '';
        $uuid = $body['uuid'] ?? '';
        $url = sprintf('%s%s', $this->config->get('api.startpin.safe_pay_nl_api_status'), $uuid);

        return $this->curlHelper->get($url, [], $apiToken);
    }


    public function approveTransaction(ServerRequestInterface $request, string $lang): JsonResponse
    {
        try {
            $postData = (array)$request->getParsedBody();
            $transactionValueObject = new TransactionValueObject($postData);

            $this->setAPIConfigData($transactionValueObject->getApiToken(), $transactionValueObject->getServiceId());
            $transaction = Transaction::get($transactionValueObject->getTransactionId());
            $isApproved = $transaction->approve();
            if (!$isApproved) {
                throw new Error($this->getTranslation('error:could_not_approve_transaction', $lang));
            }

            $message = $this->getTranslation('api_response:status_approved', $lang);
            $data = $this->prepareSuccessAPIResponseData($message);

            return new JsonResponse($data);
        } catch (FormValidationException $exception) {
            $message = sprintf('%s: %s', $exception->getMessage(), $exception->getInvalidFieldsAsString());
        } catch (ConfigException | Error $exception) {
            $message = $exception->getMessage();
        }

        $data = $this->prepareFailedAPIResponseData($message);

        return new JsonResponse($data);
    }

    public function declineTransaction(ServerRequestInterface $request, string $lang): JsonResponse
    {
        try {
            $postData = (array)$request->getParsedBody();
            $transactionValueObject = new TransactionValueObject($postData);

            $this->setAPIConfigData($transactionValueObject->getApiToken(), $transactionValueObject->getServiceId());
            $transaction = Transaction::get($transactionValueObject->getTransactionId());
            $isDeclined = $transaction->decline();
            if (!$isDeclined) {
                throw new Error($this->getTranslation('error:could_not_decline_transaction', $lang));
            }

            $message = $this->getTranslation('api_response:status_declined', $lang);
            $data = $this->prepareSuccessAPIResponseData($message);

            return new JsonResponse($data);
        } catch (FormValidationException $exception) {
            $message = sprintf('%s: %s', $exception->getMessage(), $exception->getInvalidFieldsAsString());
        } catch (ConfigException | Error $exception) {
            $message = $exception->getMessage();
        }

        $data = $this->prepareFailedAPIResponseData($message);

        return new JsonResponse($data);
    }

    public function cancelTransaction(ServerRequestInterface $request, string $lang): JsonResponse
    {
        try {
            $postData = (array)$request->getParsedBody();
            $transactionValueObject = new TransactionValueObject($postData);

            $this->setAPIConfigData($transactionValueObject->getApiToken(), $transactionValueObject->getServiceId());
            $data = Transaction::cancel($transactionValueObject->getTransactionId())->getData();
            $message = $this->getTranslation('api_response:status_cancelled', $lang);
            $apiResponseData = $this->prepareSuccessAPIResponseData($message);
            $data = array_merge($data, $apiResponseData);

            return new JsonResponse($data);
        } catch (FormValidationException $exception) {
            $message = sprintf('%s: %s', $exception->getMessage(), $exception->getInvalidFieldsAsString());
        } catch (ConfigException | Error $exception) {
            $message = $exception->getMessage();
        }

        $data = $this->prepareFailedAPIResponseData($message);

        return new JsonResponse($data);
    }

    /**
     * @param ServerRequestInterface $request
     * @return string
     * @throws ConfigException
     * @throws QrError
     */
    public function getQRCode(ServerRequestInterface $request): string
    {
        $arrData = (array)$request->getParsedBody();
        $gateway = $this->config->get('api.startpin.apiUrl');
        $arrData['gateway'] = $gateway;

        if (!empty($gateway)) {
            Config::setApiBase($gateway);
        }

        Config::setTokenCode('token');
        Config::setApiToken($arrData['api_token']);
        Config::setServiceId($arrData['service_id']);

        try {
            $arrData['returnUrl'] = $this->config->get('api.startpin.return_url');
            $transaction = Transaction::start($arrData);

            $transactionData = $transaction->getData();

            /** @var Curl $curl */
            $curl = Config::getCurl();
            $curlInfo = curl_getinfo($curl->curl);

            $result = array();
            $result['transactionId'] = $transaction->getTransactionId();
            $result['redirectUrl'] = $transaction->getRedirectUrl();
            $result['entranceCode'] = '';
            $result['paymentReference'] = $transaction->getPaymentReference();
            $result['rawData'] = json_encode($transactionData);
            $result['responseTime'] = $curlInfo['total_time'];
            $result['hash'] = $transactionData['terminal']['hash'] ?? null;

            if (isset($arrData['paymentMethod']) && $arrData['paymentMethod'] == 1927) {
                $instoreTransaction = \Paynl\Instore::status(['hash' => $result['hash']]);
                $result['instoreTransactionData'] = $instoreTransaction->getData();
                $result['instoreTransactionStatus'] = $instoreTransaction->getTransactionState();
                $result['terminalStatus'] = $instoreTransaction->getTerminalState();
                $result['percentage'] = $result['instoreTransactionData']['progress']['percentage'];
                $result['percentagePerSecond'] = $result['instoreTransactionData']['progress']['percentage_per_second'];
            }

            $arrUrl = explode('/', $result['redirectUrl']);

            foreach ($arrUrl as $segment) {
                if (preg_match('/^[a-f0-9]{40}$/i', $segment)) {
                    $result['entranceCode'] = $segment;
                }
            }

            if (empty($result['entranceCode'])) {
                $url = (string)parse_url($result['redirectUrl'], PHP_URL_QUERY);
                parse_str($url, $arrUrl);
                if (isset($arrUrl['entranceCode'])) {
                    $result['entranceCode'] = $arrUrl['entranceCode'];
                }
            }
            if (!empty($result['transactionId'])
                && !empty($result['entranceCode'])
                && empty($arrData['paymentMethod'])) {
                $result['uuid'] = UUID::encode(UUID::QR_TYPE_TRANSACTION, [
                    'orderId' => $result['transactionId'],
                    'entranceCode' => $result['entranceCode']
                ]);

                $qrPayNlUrl = sprintf('%s/', rtrim($this->config->get('api.startpin.qr_pay_nl_url'), '/'));
                $result['qr_url'] = $qrPayNlUrl . $result['uuid'];
                $parameters = http_build_query([
                    'no-logo' => 1,
                    'payload' => $result['qr_url']
                ]);
                $result['generated_qr_url'] = sprintf('%sqr?%s', $qrPayNlUrl, $parameters);
            }

            return $this->jsonData($result);
        } catch (Error | ErrorException $e) {
            return $this->jsonData(['error' => $e->getMessage()]);
        }
    }

    /**
     * @param string $token
     * @param string $lang
     * @return string
     * @throws ConfigException
     * @throws MPOSException
     */
    public function getInstoreTerminalsByTokenAndLang(string $token, string $lang):string
    {
        if (empty($token)) {
            return $this->jsonData([
                'request' => [
                    'result' => 0,
                    'errorId' => '',
                    'errorMessage' => $this->getTranslation('error:invalid_token', $lang)
                ]
            ]);
        }

        try {
            $getTerminalsApiUrl = $this->config->get('api.startpin.getTerminals');

            return $this->curlHelper->post($getTerminalsApiUrl, [], $token);
        } catch (CurlException $exception) {
            return $this->jsonData([
                'request' => [
                    'result' => 0,
                    'errorId' => '',
                    'errorMessage' => $this->getTranslation('error:could_not_get_terminals', $lang)
                ]
            ]);
        }
    }

    public function checkScannedCard(string $cardNumber, string $apiToken): string
    {
        try {
            $url = $this->config->get('api.voucher.info');
            $data = [
                PayAPIFieldEnum::CARD_NUMBER => $cardNumber
            ];

            return $this->curlHelper->get($url, $data, $apiToken);
        } catch (ConfigException | CurlException $exception) {
            $data = $this->prepareFailedAPIResponseData($exception->getMessage());

            return $this->jsonData($data);
        }
    }

    /**
     * @param mixed[] $parameters
     * @param string $apiToken
     * @param string $lang
     * @return string
     * @throws MPOSException
     */
    public function startFundGiftCard(array $parameters, string $apiToken, string $lang): string
    {
        try {
            $url = $this->config->get('api.voucher.transaction');
            $this->validateGiftCard($parameters, $apiToken, $lang);

            return $this->curlHelper->get($url, $parameters, $apiToken);
        } catch (ConfigException | CurlException | GiftCardValidationException $exception) {
            $data = $this->prepareFailedAPIResponseData($exception->getMessage());

            return $this->jsonData($data);
        }
    }

    /**
     * @param mixed[] $parameters
     * @param string $apiToken
     * @return string
     */
    public function checkScannedCardTransaction(array $parameters, string $apiToken): string
    {
        try {
            $this->setAPIConfigData($apiToken, $parameters['serviceId']);
            $data = Transaction::status($parameters['transactionId'])->getData();

            return $this->jsonData($data);
        } catch (ConfigException | Error $exception) {
            $data = $this->prepareFailedAPIResponseData($exception->getMessage());

            return $this->jsonData($data);
        }
    }

    /**
     * @param mixed[] $parameters
     * @param string $apiToken
     * @return string
     */
    public function startScannedCardTransaction(array $parameters, string $apiToken): string
    {
        try {
            $url = $this->config->get('api.startpin.qrPayment');

            return $this->curlHelper->get($url, $parameters, $apiToken);
        } catch (ConfigException | CurlException $exception) {
            $data = $this->prepareFailedAPIResponseData($exception->getMessage());

            return $this->jsonData($data);
        }
    }

    /**
     * @param ServerRequestInterface $request
     * @param string $lang
     * @return string
     * @throws MPOSException
     */
    public function doInstoreStart(ServerRequestInterface $request, string $lang): string
    {
        try {
            $postedData = (array)$request->getParsedBody();

            $ip = (string)filter_var($this->remoteAddress->getIpAddress(), FILTER_VALIDATE_IP);
            $startpinStartValueObject = new StartpinStartValueObject($postedData, $ip);

            $startTransactionApiUrl = $this->config->get('api.startpin.start');
            $dataForCallToApi = $this->prepareDataForStartTrx($startpinStartValueObject);
            $token = $startpinStartValueObject->getToken();

            return $this->curlHelper->post($startTransactionApiUrl, $dataForCallToApi, $token);
        } catch (FormValidationException $exception) {
            return $this->jsonData([
                'success' => false,
                'message' => $this->getTranslation($exception->getMessage(), $lang),
            ]);
        } catch (CurlException $exception) {
            return $this->jsonData([
                'success' => false,
                'message' => $this->getTranslation('error:could_not_start_transaction', $lang),
            ]);
        } catch (ConfigException $exception) {
            return $this->jsonData([
                'success' => false,
                'message' => $exception->getMessage(),
            ]);
        }
    }

    /**
     * @param StartpinStartValueObject $startpinStartValueObject
     * @return mixed[]
     * @throws ConfigException
     */
    private function prepareDataForStartTrx(StartpinStartValueObject $startpinStartValueObject): array
    {
        $startTrxData = [];
        $startTrxData['serviceId'] = $startpinStartValueObject->getServiceId();
        $startTrxData['amount'] = $startpinStartValueObject->getAmount();
        $startTrxData['ipAddress'] = $startpinStartValueObject->getIpAddress();
        $startTrxData['finishUrl'] = $this->config->get('api.startpin.finishUrl');

        if (!empty($startpinStartValueObject->getPaymentOptionId())) {
            $startTrxData['paymentOptionId'] = $startpinStartValueObject->getPaymentOptionId();
        }

        if (!empty($startpinStartValueObject->getTerminalId())) {
            $startTrxData['paymentOptionSubId'] = $startpinStartValueObject->getTerminalId();
        }

        if (!empty($startpinStartValueObject->getDescription())) {
            $startTrxData['transaction']['description'] = $startpinStartValueObject->getDescription();
        }

        if (!empty($startpinStartValueObject->getOrderNumber())) {
            $startTrxData['transaction']['orderNumber'] = $startpinStartValueObject->getOrderNumber();
        }

        if (!empty($startpinStartValueObject->getLanguage())) {
            $startTrxData['enduser']['language'] = $startpinStartValueObject->getLanguage();
        }

        return $startTrxData;
    }

    /**
     * @param ServerRequestInterface $request
     * @param string $lang
     * @return string
     * @throws MPOSException
     */
    public function doInstorePayment(ServerRequestInterface $request, string $lang): string
    {
        try {
            $postedData = (array)$request->getParsedBody();
            $startpinPaymentValueObject = new StartpinPaymentValueObject($postedData);

            $paymentApiUrl = $this->config->get('api.startpin.paymentHttp');
            $dataForCallToApi = $this->prepareDataForPayment($startpinPaymentValueObject);
            $token = $startpinPaymentValueObject->getToken();
            return $this->curlHelper->post($paymentApiUrl, $dataForCallToApi, $token);
        } catch (FormValidationException $exception) {
            return $this->jsonData([
                'success' => false,
                'message' => $this->getTranslation($exception->getMessage(), $lang),
            ]);
        } catch (CurlException $exception) {
            return $this->jsonData([
                'success' => false,
                'message' => $this->getTranslation('error:could_not_start_transaction', $lang),
            ]);
        } catch (ConfigException $exception) {
            return $this->jsonData([
                'success' => false,
                'message' => $exception->getMessage(),
            ]);
        }
    }

    /**
     * @param ServerRequestInterface $request
     * @param string $lang
     * @return Response
     * @throws MPOSException
     */
    public function confirmPayment(ServerRequestInterface $request, string $lang): Response
    {
        $postedData = [];
        try {
            $postedData = $this->preparePaymentConfirmationPostedData($request);
            $startpinConfirmPaymentValueObject = new StartpinConfirmPaymentValueObject($postedData);

            $confirmPaymentApiUrl = $this->config->get('api.startpin.confirmPaymentHttp');
            $dataForCallToApi = $this->preparePaymentConfirmationData($startpinConfirmPaymentValueObject);
            $requestUrl = sprintf("%s?%s", $confirmPaymentApiUrl, http_build_query($dataForCallToApi));

            $apiResponse = $this->curlHelper->get($requestUrl);

            return $this->getApplicationJavascript($apiResponse);
        } catch (FormValidationException $exception) {
            $apiResponse = $this->jsonData([
                'request' => [
                    'result' => 0,
                    'errorMessage' => $this->getTranslation($exception->getMessage(), $lang),
                ]
            ]);

            $apiResponse = $this->prepareCallbackForResponse(
                $postedData[StartpinConfirmPaymentValueObject::FIELD_CALLBACK],
                $apiResponse
            );

            return $this->getApplicationJavascript($apiResponse);
        } catch (CurlException $exception) {
            $apiResponse = $this->jsonData([
                'request' => [
                    'result' => 0,
                    'errorMessage' => $this->getTranslation(self::API_ERROR_PAY_0, $lang),
                ]
            ]);

            $apiResponse = $this->prepareCallbackForResponse(
                $postedData[StartpinConfirmPaymentValueObject::FIELD_CALLBACK],
                $apiResponse
            );

            return $this->getApplicationJavascript($apiResponse);
        } catch (ConfigException $exception) {
            return new JsonResponse([
                'success' => false,
                'message' => $exception->getMessage()
            ]);
        }
    }

    /**
     * @param string[] $parameters
     * @param string $apiToken
     * @param string $lang
     * @throws GiftCardValidationException
     * @throws MPOSException
     */
    private function validateGiftCard(array $parameters, string $apiToken, string $lang): void
    {
        if (empty($parameters['cardNumber'])) {
            throw new GiftCardValidationException('CardNumber is absent.');
        }

        $amount = $parameters['amount'];

        $scannedCardInfo = $this->checkScannedCard($parameters['cardNumber'], $apiToken);
        $cardInfo = json_decode($scannedCardInfo, true);
        if ($cardInfo['request']['result'] !== '1') {
            throw new GiftCardValidationException(
                sprintf($this->getTranslation('error:could_not_find_gift_card', $lang), $parameters['cardNumber'])
            );
        }

        $cardAvailableAmount = $cardInfo['card']['balance'];
        if ($cardAvailableAmount < $amount) {
            throw new GiftCardValidationException($this->getTranslation('error:not_enough_money', $lang));
        }
    }

    /**
     * @param StartpinPaymentValueObject $startpinPaymentValueObject
     * @return mixed[]
     */
    private function prepareDataForPayment(StartpinPaymentValueObject $startpinPaymentValueObject): array
    {
        $paymentData = [];
        $paymentData['terminalId'] = $startpinPaymentValueObject->getTerminalId();
        $paymentData['transactionId'] = $startpinPaymentValueObject->getTransactionId();

        return $paymentData;
    }

    /**
     * @param ServerRequestInterface $request
     * @return mixed[]
     */
    private function preparePaymentConfirmationPostedData(ServerRequestInterface $request): array
    {
        return array_combine(
            StartpinConfirmPaymentValueObject::REQUIRED_FIELDS,
            [
                $request->getQueryParams()[StartpinConfirmPaymentValueObject::FIELD_CALLBACK] ?? '',
                $request->getQueryParams()[StartpinConfirmPaymentValueObject::FIELD_HASH] ?? '',
                $request->getQueryParams()[StartpinConfirmPaymentValueObject::FIELD_EMAIL_ADDRESS] ?? '',
                $request->getQueryParams()[StartpinConfirmPaymentValueObject::FIELD_LANGUAGE_ID] ?? '',
            ]
        );
    }

    /**
     * @param StartpinConfirmPaymentValueObject $confirmPaymentValueObject
     * @return mixed[]
     */
    private function preparePaymentConfirmationData(StartpinConfirmPaymentValueObject $confirmPaymentValueObject): array
    {
        return array_combine(
            StartpinConfirmPaymentValueObject::REQUIRED_FIELDS,
            [
                $confirmPaymentValueObject->getCallback(),
                $confirmPaymentValueObject->getHash(),
                $confirmPaymentValueObject->getEmail(),
                $confirmPaymentValueObject->getLanguageId()
            ]
        );
    }

    private function prepareCallbackForResponse(string $callbackFunction, string $apiResponse): string
    {
        return sprintf('%s(%s);', $callbackFunction, $apiResponse);
    }

    /**
     * @param string $message
     * @param string $lang
     * @return string
     * @throws MPOSException
     */
    public function getTranslation(string $message, string $lang): string
    {
        return $this->translationsHelper->translate($lang, $message);
    }

    /**
     * @param mixed $response
     * @return string
     */
    private function jsonData($response): string
    {
        return json_encode($response) ?: '';
    }

    private function getApplicationJavascript(string $result): Response
    {
        $response = new Response();
        $response->getBody()->write($result);

        return $response->withHeader('Content-Type', 'application/javascript');
    }

    /**
     * @param string $apiToken
     * @param string $serviceId
     * @throws ConfigException
     */
    private function setAPIConfigData(string $apiToken, string $serviceId): void
    {
        $tokenCode = $this->config->get('api.auth.token_code');
        $gateway = $this->config->get('api.startpin.apiUrl');
        if (!empty($gateway)) {
            Config::setApiBase($gateway);
        }

        Config::setTokenCode($tokenCode);
        Config::setApiToken($apiToken);
        Config::setServiceId($serviceId);
    }

    /**
     * @param string $message
     * @return mixed[]
     */
    private function prepareSuccessAPIResponseData(string $message): array
    {
        return [
            PayAPIFieldEnum::REQUEST => [
                PayAPIFieldEnum::RESULT => PayAPIResultEnum::SUCCESS,
                PayAPIFieldEnum::ERROR_ID => '',
                PayAPIFieldEnum::ERROR_MESSAGE => ''
            ],
            PayAPIFieldEnum::MESSAGE => $message
        ];
    }

    /**
     * @param string $message
     * @return mixed[]
     */
    private function prepareFailedAPIResponseData(string $message): array
    {
        return [
            PayAPIFieldEnum::REQUEST => [
                PayAPIFieldEnum::RESULT => PayAPIResultEnum::FAILED,
                PayAPIFieldEnum::ERROR_ID => '',
                PayAPIFieldEnum::ERROR_MESSAGE => $message
            ]
        ];
    }
}
