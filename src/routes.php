<?php

use MPOS\Controllers\PdfController;
use MPOS\Controllers\StartpinController;
use MPOS\Controllers\TransactionController;
use MPOS\Middleware\SecureHeadersMiddleware;
use MPOS\Middleware\SessionMiddleware;
use MPOS\Enums\RoutesEnum;
use League\Route\Router;
use League\Container\Container;
use MPOS\Controllers\BarcodePaymentController;
use MPOS\Controllers\QRPaymentController;
use MPOS\Controllers\IndexController;
use MPOS\Middleware\CsrfTokenMiddleware;
use MPOS\Helpers\ConfigHelper;

/** @var Container $container */
$config = $container->get(ConfigHelper::class);

/** @var Router $router */

// Index
$router->addPatternMatcher('notRequiredSlug', '[a-zA-Z0-9_\-]*');
$router->get("/{tokenId:notRequiredSlug}", IndexController::class . '::index');
$router->post("/{tokenId:notRequiredSlug}", IndexController::class . '::index');
$router->post(RoutesEnum::INSTORE_SERVICES, IndexController::class . '::getInstoreServices');
$router->post(RoutesEnum::INSTORE_TERMINALS, IndexController::class . '::getInstoreTerminals');
$router->post(RoutesEnum::STORE_FORM_DATA, IndexController::class . '::storeFormData');
$router->post(RoutesEnum::CLEAR_FORM_DATA, IndexController::class . '::clearFormData');

// Startpin
$router->post(RoutesEnum::DO_INSTORE_START, StartpinController::class . '::doInstoreStart');
$router->post(RoutesEnum::DO_INSTORE_PAYMENT, StartpinController::class . '::doInstorePayment');
$router->get(RoutesEnum::CONFIRM_PAYMENT, StartpinController::class . '::confirmPayment');

// Barcode
$router->post(RoutesEnum::CHECK_CARD_SCAN, BarcodePaymentController::class . '::checkScannedCard');
$router->post(
    RoutesEnum::START_SCANNED_TRANSACTION,
    BarcodePaymentController::class . '::startScannedCardTransaction'
);
$router->post(
    RoutesEnum::CHECK_SCANNED_TRANSACTION,
    BarcodePaymentController::class . '::checkScannedCardTransaction'
);

// QR
$router->post(RoutesEnum::START_QR_CODE, QRPaymentController::class . '::getQRCode');

// Transaction
$router->post(RoutesEnum::APPROVE_TRANSACTION, TransactionController::class . '::approve');
$router->post(RoutesEnum::DECLINE_TRANSACTION, TransactionController::class . '::decline');
$router->post(RoutesEnum::CANCEL_TRANSACTION, TransactionController::class . '::cancel');

// PDF
$router->post(RoutesEnum::GENERATE_PDF, PdfController::class . '::generatePDF');

// Global middleware
$secureHeadersMiddleware = $container->get(SecureHeadersMiddleware::class);
$router->middleware($secureHeadersMiddleware);
$sessionMiddleware = $container->get(SessionMiddleware::class);
$router->middleware($sessionMiddleware);

if ($config->get('common.csrf.active') !== 0) {
    $csrfTokenMiddleware = $container->get(CsrfTokenMiddleware::class);
    $router->middleware($csrfTokenMiddleware);
}
