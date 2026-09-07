<?php
namespace Codilar\HelloWorld\Controller\Hello;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Controller\ResultInterface;

class Index implements ActionInterface
{
    public function __construct(
        private readonly RawFactory $rawFactory,
        private readonly RequestInterface $request
    ) {
    }
    public function execute(): ResultInterface
    {
        $name = $this->request->getParam('name', 'Guest');
        $result = $this->rawFactory->create();
        $result->setContents(
            'Hello ' . $name . '!'
        );
        return $result;
    }
}
