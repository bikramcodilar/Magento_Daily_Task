<?php
namespace Codilar\HelloWorld\Controller\Hello;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Codilar\HelloWorld\Api\GreetingInterface;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\Controller\ResultInterface;

class Greeting implements ActionInterface
{
    public function __construct(private readonly RawFactory $rawFactory, private readonly RequestInterface $request, private readonly GreetingInterface $greeting)
    {
    }
    public function execute(): ResultInterface
    {
        $name = $this->request->getParam('name', 'Guest');
        $message = $this->greeting->getMessage($name);
        $result = $this->rawFactory->create();
        $result->setContents($message);
        return $result;
    }
}

