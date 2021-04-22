<?php

namespace MPOS\Enums;

use MyCLabs\Enum\Enum;

class PayAPIFieldEnum extends Enum
{
    const REQUEST = 'request';
    const RESULT = 'result';
    const ERROR_ID = 'errorId';
    const ERROR_MESSAGE = 'errorMessage';

    const MESSAGE = 'message';
    const CARD_NUMBER = 'cardNumber';
}
