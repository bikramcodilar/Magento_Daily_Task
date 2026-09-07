<?php

namespace Codilar\HelloWorld\Controller\Index;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;

class Json implements ActionInterface
{
    public function __construct(
        private readonly JsonFactory $jsonFactory
    ) {
    }
    public function execute(): ResultInterface
    {
        $result = $this->jsonFactory->create();
        return $result->setData([
            'success' => true,
            'message' => 'Hello from Magento!',
            'module' => 'Codilar_HelloWorld'
        ]);
    }
}
