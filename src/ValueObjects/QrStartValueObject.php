<?php

namespace MPOS\ValueObjects;

use MPOS\Exceptions\FormValidationException;

class QrStartValueObject extends BaseStartValueObject
{
    const FIELD_TOKEN = 'api_token';
    const FIELD_SERVICE_ID = 'service_id';

    const REQUIRED_FIELDS = [
        self::FIELD_TOKEN,
        self::FIELD_SERVICE_ID,
        self::FIELD_AMOUNT
    ];


    /**
     * QrStartValueObject constructor.
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

        if ($this->amount < 0) {
            $this->amount = -(int)$this->amount;
        }
    }
}
