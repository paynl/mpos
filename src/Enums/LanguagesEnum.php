<?php

namespace MPOS\Enums;

use MPOS\Exceptions\MPOSException;
use MyCLabs\Enum\Enum;

class LanguagesEnum extends Enum
{
    const DEFAULT_LANG = 'nl';
    const LANG_EN = 'en';

    /**
     * @var string[]
     */
    private static $languagesLocals = [
        self::DEFAULT_LANG => 'nl_NL',
        self::LANG_EN => 'en_GB',
    ];

    /**
     * @param string $lang
     * @return string
     * @throws MPOSException
     */
    public static function getLocaleByLanguage(string $lang): string
    {
        if (!self::isValid($lang)) {
            $errorCode = ErrorCodesEnum::UNKNOWN_LOCALE_FOR_TRANSLATION;
            $error = ErrorCodesEnum::getErrorByCode($errorCode);
            $errorMessage = sprintf($error, $lang);
            throw new MPOSException($errorMessage, $errorCode);
        }

        return self::$languagesLocals[$lang] ?? self::$languagesLocals[self::DEFAULT_LANG];
    }
}
