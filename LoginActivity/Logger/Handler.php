<?php
namespace Codilar\LoginActivity\Logger;

use Monolog\Handler\StreamHandler;
use Monolog\Level;

class Handler extends StreamHandler
{
    public function __construct()
    {
        parent::__construct(
            BP . '/var/log/codilar_login_activity.log',
            Level::Info
        );
    }
}
