<?php

namespace Codilar\ProductEnquiry\Logger;

use Monolog\Logger as MonologLogger;

class Logger extends MonologLogger
{
    public function __construct(Handler $handler)
    {
        parent::__construct(
            'product_enquiry',
            [$handler]
        );
    }
}
