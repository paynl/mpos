<?php

use MPOS\Helpers\ScriptExecutionSettingsHelper;
use MPOS\Providers\ControllersProvider;
use MPOS\Providers\ExternalHelpersProvider;
use MPOS\Providers\HelpersProvider;
use MPOS\Providers\MiddlewareProvider;
use MPOS\Helpers\ConfigHelper;
use League\Container\Container;
use League\Route\Router;
use League\Route\Strategy\ApplicationStrategy;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use League\Route\Http\Exception\NotFoundException;
use League\Route\Http\Exception\MethodNotAllowedException;
use Twig\Environment;

$container = new Container();

$container->share('request', function () {
    return ServerRequestFactory::fromGlobals($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);
});

$container->share('emitter', SapiEmitter::class);
$container->addServiceProvider(new HelpersProvider());
// apply some php settings
$container->get(ScriptExecutionSettingsHelper::class);

$container->addServiceProvider(new ControllersProvider());
$container->addServiceProvider(new ExternalHelpersProvider());
$container->addServiceProvider(new MiddlewareProvider());
$config = $container->get(ConfigHelper::class);

$strategy = new ApplicationStrategy();
$strategy->setContainer($container);
$router = new Router();
$router->setStrategy($strategy);

require_once 'routes.php';

try {
    $response = $router->dispatch($container->get('request'));
    $container->get('emitter')->emit($response);
} catch (NotFoundException | MethodNotAllowedException $httpException) {
    header("HTTP/1.0 404 Not Found");
    $page404Path = $config->get('common.page_404_path');
    require_once($page404Path);
} catch (Throwable $exception) {
    if ($config->get('common.display_errors') === 1) {
        throw $exception;
    }

    error_log($exception);
}
