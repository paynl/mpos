<?php

namespace MPOS\ValueObjects;

use MPOS\Exceptions\FormValidationException;

class StartpinPaymentValueObject
{
    const FIELD_TOKEN = 'token';
    const FIELD_TERMINAL_ID = 'terminalId';
    const FIELD_TRANSACTION_ID = 'transactionId';

    const REQUIRED_FIELDS = [
        self::FIELD_TOKEN,
        self::FIELD_TERMINAL_ID,
        self::FIELD_TRANSACTION_ID,
    ];

    const REQUIRED_POST_DATA_FIELDS_MISSING_ERROR = 'error:missing_required_field';

    /** @var string */
    private $token;

    /** @var string */
    private $terminalId;

    /** @var string */
    private $transactionId;

    /**
     * TrxSearchValueObject constructor.
     * @param mixed[] $postedData
     * @throws FormValidationException
     */
    public function __construct(array $postedData)
    {
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
        $this->terminalId =
            (string)filter_var(urldecode($postedData[self::FIELD_TERMINAL_ID]), FILTER_SANITIZE_STRIPPED);
        $this->transactionId =
            (string)filter_var(urldecode($postedData[self::FIELD_TRANSACTION_ID]), FILTER_SANITIZE_STRIPPED);
    }

    public function getToken(): string
    {
        return $this->token;
    }

    public function getTerminalId(): string
    {
        return $this->terminalId;
    }

    public function getTransactionId(): string
    {
        return $this->transactionId;
    }
}
