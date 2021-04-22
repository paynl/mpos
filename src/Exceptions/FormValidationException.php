<?php

namespace MPOS\Exceptions;

use Throwable;

class FormValidationException extends MPOSException
{
    /** @var mixed[] */
    private $invalidFields;

    /** @var mixed[] */
    private $particularFieldsErrors;

    /**
     * FormValidationException constructor.
     * @param string $message
     * @param mixed[] $invalidFields
     * @param mixed[] $particularFieldsErrors
     * @param int $code
     * @param Throwable|null $previous
     */
    public function __construct(
        string $message,
        array $invalidFields = [],
        array $particularFieldsErrors = [],
        int $code = 0,
        Throwable $previous = null
    ) {
        $this->invalidFields = $invalidFields;
        $this->particularFieldsErrors = $particularFieldsErrors;
        parent::__construct($message, $code, $previous);
    }

    /**
     * @return mixed[]
     */
    public function getInvalidFields(): array
    {
        return $this->invalidFields;
    }

    /**
     * @return string
     */
    public function getInvalidFieldsAsString(): string
    {
        return implode(',', $this->invalidFields);
    }

    /**
     * @return mixed[]
     */
    public function getParticularFieldsErrors(): array
    {
        return $this->particularFieldsErrors;
    }
}
