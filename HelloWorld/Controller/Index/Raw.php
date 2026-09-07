<?php
namespace Codilar\HelloWorld\Controller\Index;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Controller\ResultInterface;

class Raw implements ActionInterface
{
    public function __construct(
        private readonly RawFactory $rawFactory,
    ) {
    }
    public function execute(): ResultInterface
    {
        $result = $this->rawFactory->create();
        $result->setContents('This is the Raw action.');
        return $result;
    }
}
