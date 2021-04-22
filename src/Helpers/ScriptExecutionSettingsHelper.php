<?php

namespace MPOS\Helpers;

use Psr\Http\Message\ServerRequestInterface;
use MPOS\Exceptions\ConfigException;
use Whoops\Handler\PrettyPageHandler;
use Whoops\Run;

class ScriptExecutionSettingsHelper
{
    /**
     * ScriptExecutionSettingsHelper constructor.
     * @param ServerRequestInterface $request
     * @param ConfigHelper $config
     * @param Run $whoops
     * @param PrettyPageHandler $prettyPageHandler
     * @throws ConfigException
     */
    public function __construct(
        ServerRequestInterface $request,
        ConfigHelper $config,
        Run $whoops,
        PrettyPageHandler $prettyPageHandler
    ) {
        set_time_limit($config->get('common.time_limit'));
        error_reporting($config->get('common.error_reporting_level'));
        ini_set('display_errors', $config->get('common.display_errors'));
        if ($config->get('common.display_errors') === 1 && $request->getMethod() === 'GET') {
            $whoops->prependHandler($prettyPageHandler);
            $whoops->register();
        }
    }
}
