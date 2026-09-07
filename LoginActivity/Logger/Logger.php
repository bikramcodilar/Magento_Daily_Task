<?php

namespace Codilar\LoginActivity\Logger;

use Monolog\Logger as MonologLogger;

class Logger extends MonologLogger
{
    public function __construct(
        Handler $handler
    ) {
        parent::__construct(
            'codilar_login_activity',
            [$handler]
        );
    }
}
