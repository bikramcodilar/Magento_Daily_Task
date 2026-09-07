<?php
namespace Codilar\HelloWorld\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;

class Config
{
    private const string XML_PATH_GREETING_MESSAGE = 'codilar_helloworld/general/greeting_message';

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig
    ) {
    }

    public function getGreetingMessage(): string
    {
        return (string)$this->scopeConfig->getValue(
            self::XML_PATH_GREETING_MESSAGE
        );
    }
}
