<?php

namespace MPOS\ValueObjects;

use MPOS\Exceptions\FormValidationException;

class BaseStartValueObject
{
    const FIELD_AMOUNT = 'amount';
    const FIELD_DESCRIPTION = 'description';
    const FIELD_ORDER_NUMBER = 'orderNumber';

    const REQUIRED_POST_DATA_FIELDS_MISSING_ERROR = 'error:missing_required_field';

    const ORDER_NUMBER_MAX_LENGTH = 16;

    /** @var string */
    protected $token;

    /** @var string */
    protected $serviceId;

    /** @var string */
    protected $terminalId;

    /** @var integer */
    protected $amount;

    /** @var string */
    protected $ipAddress;

    /** @var string */
    protected $paymentOptionId = '';

    /** @var string */
    protected $description = '';

    /** @var string */
    protected $orderNumber = '';

    /** @var string */
    protected $language = '';

    /** @var string */
    protected $finishUrl;

    /**
     * @param mixed[] $postedData
     * @param string[] $requiredFields
     * @throws FormValidationException
     */
    protected function checkRequiredFields(array $postedData, array $requiredFields): void
    {
        $missedFields = [];
        foreach ($requiredFields as $requiredFieldName) {
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
    protected function fillCommonFields(array $postedData): void
    {
        if (isset($postedData[self::FIELD_DESCRIPTION])) {
            $this->description =
                (string)filter_var(urldecode($postedData[self::FIELD_DESCRIPTION]), FILTER_SANITIZE_STRIPPED);
        }

        if (isset($postedData[self::FIELD_ORDER_NUMBER])) {
            $orderNumber =
                (string)filter_var(urldecode($postedData[self::FIELD_ORDER_NUMBER]), FILTER_SANITIZE_STRIPPED);
            $this->orderNumber = substr($orderNumber, 0, self::ORDER_NUMBER_MAX_LENGTH);
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

    /**
     * @return mixed[]
     */
    public function getDataForTransactionStart(): array
    {
        $startTrxData = [];
        $startTrxData['serviceId'] = $this->getServiceId();
        $startTrxData['amount'] = $this->getAmount();
        $startTrxData['ipAddress'] = $this->getIpAddress();
        $startTrxData['finishUrl'] = $this->finishUrl;

        if (!empty($this->getPaymentOptionId())) {
            $startTrxData['paymentOptionId'] = $this->getPaymentOptionId();
        }

        if (!empty($this->getTerminalId())) {
            $startTrxData['paymentOptionSubId'] = $this->getTerminalId();
        }

        if (!empty($this->getDescription())) {
            $startTrxData['transaction']['description'] = $this->getDescription();
        }

        if (!empty($this->getOrderNumber())) {
            $startTrxData['transaction']['orderNumber'] = $this->getOrderNumber();
        }

        if (!empty($this->getLanguage())) {
            $startTrxData['enduser']['language'] = $this->getLanguage();
        }

        return $startTrxData;
    }
}
