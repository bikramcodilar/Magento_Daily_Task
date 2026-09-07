<?php

namespace Codilar\ProductEnquiry\Logger;

use Monolog\Handler\StreamHandler;
use Monolog\Level;

class Handler extends StreamHandler
{
    public function __construct()
    {
        parent::__construct(
            BP . '/var/log/product_enquiry.log',
            Level::Info
        );
    }
}
