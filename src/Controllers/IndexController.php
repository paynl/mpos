<?php

namespace MPOS\Controllers;

use EasyCSRF\Interfaces\SessionProvider;
use MPOS\Enums\HttpMethodEnum;
use MPOS\Helpers\CookieHelper;
use MPOS\Helpers\ApiHelper;
use MPOS\Enums\LanguagesEnum;
use MPOS\Exceptions\ConfigException;
use MPOS\Exceptions\MPOSException;
use Psr\Http\Message\ServerRequestInterface;
use Laminas\Diactoros\Response;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class IndexController extends BaseController
{
    const DESCRIPTION = 'description';
    const ORDER_NUMBER = 'order_number';
    const API_TOKEN = 'api_token';
    const SERVICE_ID = 'service_id';
    const TERMINAL_ID = 'terminal_id';
    const DEFAULT_TERMINAL_ID = 'default_terminal_id';
    const TOGGLE = 'toggle';
    const STATIC_FIELDS = 'static_fields';
    const AMOUNT = 'amount';
    const ACTIVETAB = 'activetab';

    /** @var string */
    private $view = 'index.twig';

    /** @var ApiHelper */
    private $apiHelper;

    /** @var CookieHelper */
    private $cookieHelper;

    /** @var SessionProvider */
    private $session;

    public function __construct(ApiHelper $apiHelper, CookieHelper $cookieHelper, SessionProvider $session)
    {
        $this->apiHelper = $apiHelper;
        $this->cookieHelper = $cookieHelper;
        $this->session = $session;
    }

    /**
     * @param ServerRequestInterface $request
     * @return Response
     * @throws ConfigException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     * @throws MPOSException
     */
    public function index(ServerRequestInterface $request): Response
    {
        $parameters = $this->prepareData($request);

        $this->cookieHelper->setCookies($request->getCookieParams());
        $apiToken = $this->getCookieParameter(self::API_TOKEN, true, $parameters[self::API_TOKEN]);

        $serviceIds = [];
        $terminalIds = [];
        $lang = $this->getLangFromRequest($request);
        $notSelectedLanguages = $this->getNotSelectedLangs($lang);

        $objServices = json_decode($this->apiHelper->getInstoreServicesByTokenAndLang($apiToken, $lang));
        if ((bool)$objServices->request->result === true) {
            $serviceIds = $objServices->services;
        }

        $objTerminals = json_decode($this->apiHelper->getInstoreTerminalsByTokenAndLang($apiToken, $lang));
        if ((bool)$objTerminals->request->result === true) {
            $terminalIds = $objTerminals->terminals;
        }

        $staticFields = $this->getCookieParameter(self::STATIC_FIELDS, false, 'false');
        $amount = $this->getCookieParameter(self::AMOUNT, false, $parameters[self::AMOUNT]);
        if (!empty($amount)) {
            $amount = round($amount / 100, 2);
        }

        $orderNumber = $this->getCookieParameter(self::ORDER_NUMBER, false, $parameters[self::ORDER_NUMBER]);

        $content = $this->getTwig()->render($this->view, [
            'api_token' => $apiToken,
            'amount' => $amount,
            'description' => $this->getCookieParameter(self::DESCRIPTION, false, $parameters[self::DESCRIPTION]),
            'order_number' => $orderNumber,
            'service_id' => $this->getCookieParameter(self::SERVICE_ID, true, $parameters[self::SERVICE_ID]),
            'default_terminal_id' =>
                $this->getCookieParameter(self::DEFAULT_TERMINAL_ID, true, $parameters[self::TERMINAL_ID]),
            'toggle' => $this->getCookieParameter(self::TOGGLE, false, 'true'),
            'static_fields' => $staticFields,
            'serviceIds' => $serviceIds,
            'terminalIds' => $terminalIds,
            'lang' => $lang,
            'notSelectedLanguages' => $notSelectedLanguages,
            'activeTab' => $this->getCookieParameter(self::ACTIVETAB, false),
            'safe_pay_nl_api_status' => $this->getConfig('api.startpin.safe_pay_nl_api_status'),
        ]);

        return $this->response($content);
    }

    /**
     * @param ServerRequestInterface $request
     * @return Response
     * @throws ConfigException
     * @throws MPOSException
     */
    public function getInstoreServices(ServerRequestInterface $request): Response
    {
        $lang = $this->getLangFromRequest($request);
        $apiResponse = $this->apiHelper->getInstoreServices($request, $lang);

        return $this->response($apiResponse);
    }

    /**
     * @param ServerRequestInterface $request
     * @return Response
     * @throws ConfigException
     * @throws MPOSException
     */
    public function getInstoreTerminals(ServerRequestInterface $request): Response
    {
        $lang = $this->getLangFromRequest($request);
        $apiResponse = $this->apiHelper->getInstoreTerminals($request, $lang);

        return $this->response($apiResponse);
    }

    /**
     * @param string $selectedLang
     * @return mixed[]
     */
    private function getNotSelectedLangs(string $selectedLang): array
    {
        $languagesValues = array_values(LanguagesEnum::toArray());

        return array_diff($languagesValues, [$selectedLang]);
    }

    /**
     * @param ServerRequestInterface $request
     * @return mixed[]
     */
    private function prepareData(ServerRequestInterface $request): array
    {
        if ($request->getMethod() === HttpMethodEnum::GET) {
            $parameters = $request->getQueryParams();
        } elseif ($request->getMethod() === HttpMethodEnum::POST) {
            $parameters = (array)$request->getParsedBody();
        }

        $data = [];
        $data[self::API_TOKEN] = filter_var($parameters[self::API_TOKEN] ?? '', FILTER_SANITIZE_STRIPPED);
        $data[self::SERVICE_ID] = filter_var($parameters[self::SERVICE_ID] ?? '', FILTER_SANITIZE_STRIPPED);
        $data[self::TERMINAL_ID] = filter_var($parameters[self::TERMINAL_ID] ?? '', FILTER_SANITIZE_STRIPPED);
        $rawAmount = (string)filter_var($parameters[self::AMOUNT] ?? '', FILTER_SANITIZE_STRIPPED);
        $amount = $this->formatAmount($rawAmount);
        if ($request->getMethod() === HttpMethodEnum::POST) {
            $amount *= 100;
        }
        $data[self::AMOUNT] = $amount;
        $description = $parameters[self::DESCRIPTION] ?? '';
        $data[self::DESCRIPTION] = urldecode((string)filter_var($description, FILTER_SANITIZE_STRIPPED));
        $orderNumber = $parameters[self::ORDER_NUMBER] ?? '';
        $data[self::ORDER_NUMBER] = urldecode((string)filter_var($orderNumber, FILTER_SANITIZE_STRIPPED));

        return $data;
    }

    /**
     * @param ServerRequestInterface $request
     * @return Response
     * @throws ConfigException
     */
    public function storeFormData(ServerRequestInterface $request): Response
    {
        $postedData = (array)$request->getParsedBody();

        $host = $request->getUri()->getHost();

        $this->cookieHelper->setHost($host);
        // Store not secure data to cookies
        $this->cookieHelper->saveCookieValue(self::DESCRIPTION, $postedData[self::DESCRIPTION]);
        $this->cookieHelper->saveCookieValue(self::ORDER_NUMBER, $postedData[self::ORDER_NUMBER]);
        $this->cookieHelper->saveCookieValue(self::TOGGLE, $postedData[self::TOGGLE]);
        $this->cookieHelper->saveCookieValue(self::STATIC_FIELDS, $postedData[self::STATIC_FIELDS]);
        $this->cookieHelper->saveCookieValue(self::AMOUNT, $postedData[self::AMOUNT]);

        // Store secure data to cookies
        $this->cookieHelper->saveCookieValue(self::API_TOKEN, $postedData[self::API_TOKEN], true);
        $this->cookieHelper->saveCookieValue(self::SERVICE_ID, $postedData[self::SERVICE_ID], true);
        $this->cookieHelper->saveCookieValue(self::DEFAULT_TERMINAL_ID, $postedData[self::DEFAULT_TERMINAL_ID], true);

        // 200 ok with blank response
        return $this->response('');
    }

    /**
     * @param ServerRequestInterface $request
     * @return Response
     */
    public function clearFormData(ServerRequestInterface $request): Response
    {
        $host = $request->getUri()->getHost();

        $this->cookieHelper->setHost($host);
        $this->cookieHelper->deleteCookie(self::DESCRIPTION);
        $this->cookieHelper->deleteCookie(self::ORDER_NUMBER);
        $this->cookieHelper->deleteCookie(self::API_TOKEN);
        $this->cookieHelper->deleteCookie(self::SERVICE_ID);
        $this->cookieHelper->deleteCookie(self::DEFAULT_TERMINAL_ID);
        $this->cookieHelper->deleteCookie(self::TOGGLE);
        $this->cookieHelper->deleteCookie(self::STATIC_FIELDS);
        $this->cookieHelper->deleteCookie(self::AMOUNT);

        // 200 ok with blank response
        return $this->response('');
    }

    private function formatAmount(string $rawAmount): float
    {
        return (float)str_replace(',', '.', $rawAmount);
    }

    /**
     * @param string $cookieKey
     * @param bool $isEncrypted
     * @param string $default
     * @return string
     * @throws ConfigException
     */
    private function getCookieParameter(string $cookieKey, bool $isEncrypted = false, $default = ''): string
    {
        $valueFromCookie = $this->cookieHelper->getCookieValue($cookieKey, $isEncrypted);
        $valueFromSession = $this->session->get($cookieKey);

        return $valueFromCookie ?: $valueFromSession ?: $default;
    }
}
