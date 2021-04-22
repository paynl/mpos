<?php

namespace MPOS\Enums;

use MyCLabs\Enum\Enum;

class ErrorCodesEnum extends Enum
{
    // System errors
    const CONFIG_FILE_NOT_FOUND = 6000;
    const CONFIG_KEY_NOT_FOUND = 6001;
    const CONFIG_NOT_LOADED = 6002;
    const CONFIG_BAD_FORMAT = 6003;
    const CONFIG_NOT_FOUND = 6004;
    const UNKNOWN_LOCALE_FOR_TRANSLATION = 6006;

    /** @var string[] */
    public static $errorMessages = [
        self::CONFIG_NOT_FOUND => 'Config not found.',
        self::CONFIG_FILE_NOT_FOUND => 'File not found: %s.',
        self::CONFIG_KEY_NOT_FOUND => '\'%s\' not found in config.',
        self::CONFIG_NOT_LOADED => 'Config not loaded.',
        self::CONFIG_BAD_FORMAT => 'Bad config format.',
        self::UNKNOWN_LOCALE_FOR_TRANSLATION => 'Locale for language %s is absent.',
    ];

    public static function getErrorByCode(int $errorCode): string
    {
        return self::$errorMessages[$errorCode] ?? 'Unknown error';
    }
}
