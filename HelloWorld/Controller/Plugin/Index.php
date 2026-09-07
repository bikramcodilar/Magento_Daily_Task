<?php

namespace Codilar\HelloWorld\Controller\Plugin;

use Codilar\HelloWorld\Model\Message;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;

class Index implements ActionInterface
{
    public function __construct(
        private readonly JsonFactory $jsonFactory,
        private readonly Message $message
    ) {
    }
    public function execute(): ResultInterface
    {
        $result = $this->jsonFactory->create();
        return $result->setData([
            'message' => $this->message->getMessage()
        ]);
    }
}
