<?php

namespace MPOS\ValueObjects;

use MPOS\Exceptions\FormValidationException;

class StartpinStartValueObject
{
    const FIELD_TOKEN = 'token';
    const FIELD_SERVICE_ID = 'serviceId';
    const FIELD_TERMINAL_ID = 'terminalId';
    const FIELD_AMOUNT = 'amount';
    const FIELD_IP_ADDRESS = 'ipAddress';
    const FIELD_PAYMENT_OPTION_ID = 'paymentOptionId';
    const FIELD_DESCRIPTION = 'description';
    const FIELD_ORDER_NUMBER = 'orderNumber';
    const FIELD_LANGUAGE = 'language';

    const ORDER_NUMBER_MAX_LENGTH = 16;

    const REQUIRED_FIELDS = [
        self::FIELD_TOKEN,
        self::FIELD_SERVICE_ID,
        self::FIELD_AMOUNT
    ];

    const REQUIRED_POST_DATA_FIELDS_MISSING_ERROR = 'error:missing_required_field';
    const REFUND_PIN_ID = '2351';

    /** @var string */
    private $token;

    /** @var string */
    private $serviceId;

    /** @var string */
    private $terminalId;

    /** @var integer */
    private $amount;

    /** @var string */
    private $ipAddress;

    /** @var string */
    private $paymentOptionId = '';

    /** @var string */
    private $description = '';

    /** @var string */
    private $orderNumber = '';

    /** @var string */
    private $language = '';

    /**
     * StartpinStartValueObject constructor.
     * @param mixed[] $postedData
     * @param string $userIp
     * @throws FormValidationException
     */
    public function __construct(array $postedData, string $userIp)
    {
        $this->ipAddress = $userIp;
        $this->checkRequiredFields($postedData);
        $this->prepareFields($postedData);
    }

    /**
     * @param mixed[] $postedData
     * @throws FormValidationException
     */
    private function checkRequiredFields(array $postedData): void
    {
        $missedFields = [];
        foreach (self::REQUIRED_FIELDS as $requiredFieldName) {
            if (!isset($postedData[$requiredFieldName])) {
                $missedFields[] = $requiredFieldName;
            }
        }

        if (!empty($missedFields)) {
            throw new FormValidationException(self::REQUIRED_POST_DATA_FIELDS_MISSING_ERROR, $missedFields);
        }
    }

    /**
     * @param mixed[] $postedData
     */
    private function prepareFields(array $postedData): void
    {
        $this->token = (string)filter_var(urldecode($postedData[self::FIELD_TOKEN]), FILTER_SANITIZE_STRIPPED);
        $this->serviceId = (string)filter_var(urldecode($postedData[self::FIELD_SERVICE_ID]), FILTER_SANITIZE_STRIPPED);
        $this->amount = (int)filter_var(urldecode($postedData[self::FIELD_AMOUNT]), FILTER_SANITIZE_NUMBER_INT);

        if (isset($postedData[self::FIELD_PAYMENT_OPTION_ID])) {
            $this->paymentOptionId =
                (string)filter_var(urldecode($postedData[self::FIELD_PAYMENT_OPTION_ID]), FILTER_SANITIZE_STRIPPED);
        }

        if ($this->amount < 0) {
            $this->amount = -(int)$this->amount;
            $this->paymentOptionId = self::REFUND_PIN_ID;
        }

        if (isset($postedData[self::FIELD_TERMINAL_ID])) {
            $this->terminalId =
                (string)filter_var(urldecode($postedData[self::FIELD_TERMINAL_ID]), FILTER_SANITIZE_STRIPPED);
        }

        if (isset($postedData[self::FIELD_DESCRIPTION])) {
            $this->description =
                (string)filter_var(urldecode($postedData[self::FIELD_DESCRIPTION]), FILTER_SANITIZE_STRIPPED);
        }

        if (isset($postedData[self::FIELD_ORDER_NUMBER])) {
            $orderNumber =
                (string)filter_var(urldecode($postedData[self::FIELD_ORDER_NUMBER]), FILTER_SANITIZE_STRIPPED);
            $this->orderNumber = substr($orderNumber, 0, self::ORDER_NUMBER_MAX_LENGTH);
        }

        if (isset($postedData[self::FIELD_LANGUAGE])) {
            $this->language =
                (string)filter_var(urldecode($postedData[self::FIELD_LANGUAGE]), FILTER_SANITIZE_STRIPPED);
        }
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getServiceId(): string
    {
        return $this->serviceId;
    }

    public function getTerminalId(): ?string
    {
        return $this->terminalId;
    }

    public function getAmount(): int
    {
        return $this->amount;
    }

    public function getIpAddress(): string
    {
        return $this->ipAddress;
    }

    public function getPaymentOptionId(): string
    {
        return $this->paymentOptionId;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getOrderNumber(): string
    {
        return $this->orderNumber;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }
}
