<?php
namespace Codilar\HelloWorld\Controller\Index;

use Codilar\HelloWorld\Model\Config;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;

class TestConfig implements ActionInterface
{
    public function __construct(
        private readonly JsonFactory $jsonFactory,
        private readonly Config      $config
    ) {
    }

    public function execute(): ResultInterface
    {
        $result = $this->jsonFactory->create();
        return $result->setData([
            'message' => $this->config->getGreetingMessage()
        ]);
    }
}
