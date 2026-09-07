<?php
namespace Codilar\HelloWorld\Controller\Index;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Controller\ResultInterface;

class Redirect implements ActionInterface
{
    public function __construct(
        private readonly RedirectFactory $resultRedirectFactory
    ) {
    }
    public function execute(): ResultInterface
    {
        $result = $this->resultRedirectFactory->create();
        return $result->setPath('cms/index/index');
    }
}
