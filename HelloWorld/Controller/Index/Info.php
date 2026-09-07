<?php
namespace Codilar\HelloWorld\Controller\Index;

use Codilar\HelloWorld\Api\MessageInterface;
use Codilar\HelloWorld\Model\Calculator;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;

class Info implements ActionInterface
{
    public function __construct(
        private readonly JsonFactory       $jsonFactory,
        private readonly MessageInterface $message,
        private readonly Calculator        $calculator
    ) {
    }

    public function execute(): ResultInterface
    {
        $result = $this->jsonFactory->create();
        return $result->setData([
            'module' => 'Codilar_HelloWorld',
            'message' => $this->message->getMessage(),
            'calculation' => $this->calculator->add(10, 20)

        ]);
    }
}
