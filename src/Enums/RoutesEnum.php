<?php

namespace MPOS\Enums;

use MyCLabs\Enum\Enum;

class RoutesEnum extends Enum
{
    const ROOT = '/';
    const INSTORE_SERVICES = '/{lang}/ajax/getInstoreServices';
    const INSTORE_TERMINALS = '/{lang}/ajax/getInstoreTerminals';
    const CHECK_CARD_SCAN = '/ajax/checkScannedCard';
    const START_SCANNED_TRANSACTION = '/ajax/startScannedCardTransaction';
    const START_QR_CODE = '/ajax/getQRCode';
    const APPROVE_TRANSACTION = '/{lang}/ajax/approve-transaction';
    const DECLINE_TRANSACTION = '/{lang}/ajax/decline-transaction';
    const CANCEL_TRANSACTION = '/{lang}/ajax/cancel-transaction';
    const DO_INSTORE_START = '/{lang}/ajax/doInstoreStart';
    const DO_INSTORE_PAYMENT = '/{lang}/ajax/doInstorePayment';
    const CONFIRM_PAYMENT = '/{lang}/ajax/confirmPayment';
    const CHECK_SCANNED_TRANSACTION = '/ajax/checkScannedCardTransaction';
    const GENERATE_PDF = '/pdf/generate';
    const STORE_FORM_DATA = '/ajax/storeFormData';
    const CLEAR_FORM_DATA = '/ajax/clearFormData';
}
