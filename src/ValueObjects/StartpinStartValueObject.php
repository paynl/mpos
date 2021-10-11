<?php

namespace MPOS\ValueObjects;

use MPOS\Exceptions\FormValidationException;

class StartpinStartValueObject extends BaseStartValueObject
{
    const FIELD_TOKEN = 'token';
    const FIELD_SERVICE_ID = 'serviceId';
    const FIELD_TERMINAL_ID = 'terminalId';
    const FIELD_PAYMENT_OPTION_ID = 'paymentOptionId';
    const FIELD_LANGUAGE = 'language';

    const REQUIRED_FIELDS = [
        self::FIELD_TOKEN,
        self::FIELD_SERVICE_ID,
        self::FIELD_AMOUNT
    ];

    const REFUND_PIN_ID = '2351';

    /**
     * StartpinStartValueObject constructor.
     * @param mixed[] $postedData
     * @param string $userIp
     * @param string $finishUrl
     * @throws FormValidationException
     */
    public function __construct(array $postedData, string $userIp, string $finishUrl)
    {
        $this->ipAddress = $userIp;
        $this->checkRequiredFields($postedData, self::REQUIRED_FIELDS);
        $this->prepareFields($postedData);
        $this->finishUrl = $finishUrl;
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

        if (isset($postedData[self::FIELD_LANGUAGE])) {
            $this->language =
                (string)filter_var(urldecode($postedData[self::FIELD_LANGUAGE]), FILTER_SANITIZE_STRIPPED);
        }
    }
}
