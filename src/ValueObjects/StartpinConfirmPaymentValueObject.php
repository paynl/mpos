<?php

namespace MPOS\ValueObjects;

use MPOS\Exceptions\FormValidationException;

class StartpinConfirmPaymentValueObject
{
    const FIELD_CALLBACK = 'callback';
    const FIELD_HASH = 'hash';
    const FIELD_EMAIL_ADDRESS = 'emailAddress';
    const FIELD_LANGUAGE_ID = 'languageId';

    const REQUIRED_FIELDS = [
        self::FIELD_CALLBACK,
        self::FIELD_HASH,
        self::FIELD_EMAIL_ADDRESS,
        self::FIELD_LANGUAGE_ID,
    ];

    const REQUIRED_POST_DATA_FIELDS_MISSING_ERROR = 'error:missing_required_field';

    /** @var string */
    private $callback;

    /** @var string */
    private $hash;

    /** @var string */
    private $email;

    /** @var string */
    private $languageId;

    /**
     * StartpinConfirmPaymentValueObject constructor.
     * @param mixed[] $postedData
     * @throws FormValidationException
     */
    public function __construct(array $postedData)
    {
        $this->checkRequiredFields($postedData);
        $this->prepareFields($postedData);
    }

    public function getCallback(): string
    {
        return $this->callback;
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getLanguageId(): string
    {
        return $this->languageId;
    }

    /**
     * @param mixed[] $postedData
     * @throws FormValidationException
     */
    private function checkRequiredFields(array $postedData): void
    {
        $missedFields = [];
        foreach (self::REQUIRED_FIELDS as $requiredFieldName) {
            if (empty($postedData[$requiredFieldName])) {
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
        $this->callback = (string)filter_var(urldecode($postedData[self::FIELD_CALLBACK]), FILTER_SANITIZE_STRIPPED);
        $this->hash = (string)filter_var(urldecode($postedData[self::FIELD_HASH]), FILTER_SANITIZE_STRIPPED);
        $this->email = (string)filter_var(urldecode($postedData[self::FIELD_EMAIL_ADDRESS]), FILTER_SANITIZE_EMAIL);
        $this->languageId =
            (string)filter_var(urldecode($postedData[self::FIELD_LANGUAGE_ID]), FILTER_SANITIZE_STRIPPED);
    }
}
