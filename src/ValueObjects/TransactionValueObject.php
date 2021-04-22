<?php

namespace MPOS\ValueObjects;

use MPOS\Exceptions\FormValidationException;

class TransactionValueObject
{
    const FIELD_API_TOKEN = 'api_token';
    const FIELD_SERVICE_ID = 'service_id';
    const FIELD_TRANSACTION_ID = 'transactionId';

    const REQUIRED_FIELDS = [
        self::FIELD_API_TOKEN,
        self::FIELD_SERVICE_ID,
        self::FIELD_TRANSACTION_ID
    ];

    const REQUIRED_POST_DATA_FIELDS_MISSING_ERROR = 'error:missing_required_field';

    /** @var string */
    private $apiToken;

    /** @var string */
    private $serviceId;

    /** @var string */
    private $transactionId;

    /**
     * @param mixed[] $postData
     * @throws FormValidationException
     */
    public function __construct(array $postData)
    {
        $this->checkRequiredFields($postData);
        $this->prepareFields($postData);
    }

    public function getApiToken(): string
    {
        return (string)$this->apiToken;
    }

    public function getServiceId(): string
    {
        return (string)$this->serviceId;
    }

    public function getTransactionId(): string
    {
        return (string)$this->transactionId;
    }

    /**
     * @param mixed[] $postData
     * @throws FormValidationException
     */
    private function checkRequiredFields(array $postData): void
    {
        $missedFields = [];
        foreach (self::REQUIRED_FIELDS as $requiredFieldName) {
            if (!isset($postData[$requiredFieldName])) {
                $missedFields[] = $requiredFieldName;
            }
        }

        if (!empty($missedFields)) {
            throw new FormValidationException(self::REQUIRED_POST_DATA_FIELDS_MISSING_ERROR, $missedFields);
        }
    }

    /**
     * @param mixed[] $postData
     */
    private function prepareFields(array $postData): void
    {
        $this->apiToken = (string)filter_var(urldecode($postData[self::FIELD_API_TOKEN]), FILTER_SANITIZE_STRIPPED);
        $this->serviceId = (string)filter_var(urldecode($postData[self::FIELD_SERVICE_ID]), FILTER_SANITIZE_STRIPPED);
        $this->transactionId =
            (string)filter_var(urldecode($postData[self::FIELD_TRANSACTION_ID]), FILTER_SANITIZE_STRIPPED);
    }
}
