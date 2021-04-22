<?php

namespace MPOS\Providers;

use EasyCSRF\NativeSessionProvider;
use MPOS\Controllers\IndexController;
use MPOS\Controllers\PdfController;
use MPOS\Controllers\BarcodePaymentController;
use MPOS\Controllers\QRPaymentController;
use MPOS\Controllers\StartpinController;
use League\Container\Container;
use League\Route\ContainerAwareInterface;
use League\Container\ServiceProvider\AbstractServiceProvider;
use MPOS\Controllers\TransactionController;
use MPOS\Helpers\CookieHelper;
use MPOS\Helpers\ApiHelper;

class ControllersProvider extends AbstractServiceProvider
{
    /** @var string[] */
    protected $provides = [
        PdfController::class,
        StartpinController::class,
        QRPaymentController::class,
        BarcodePaymentController::class,
        TransactionController::class,
        IndexController::class,
    ];

    public function register()
    {
        /** @var Container $container */
        $container = $this->getContainer();

        $container->share(PdfController::class, function () {
            return new PdfController();
        });

        $container->share(IndexController::class, function () use ($container) {
            return new IndexController(
                $container->get(ApiHelper::class),
                $container->get(CookieHelper::class),
                $container->get(NativeSessionProvider::class)
            );
        });

        $container->share(StartpinController::class, function () use ($container) {
            return new StartpinController(
                $container->get(ApiHelper::class)
            );
        });

        $container->share(QRPaymentController::class, function () use ($container) {
            return new QRPaymentController(
                $container->get(ApiHelper::class)
            );
        });

        $container->share(BarcodePaymentController::class, function () use ($container) {
            return new BarcodePaymentController(
                $container->get(ApiHelper::class)
            );
        });

        $container->share(TransactionController::class, function () use ($container) {
            return new TransactionController(
                $container->get(ApiHelper::class)
            );
        });

        $container->inflector(ContainerAwareInterface::class)->invokeMethod('setContainer', [$container]);
    }
}
