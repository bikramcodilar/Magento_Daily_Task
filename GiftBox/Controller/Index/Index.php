<?php

declare(strict_types=1);

namespace Codilar\GiftBox\Controller\Index;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;

class Index implements ActionInterface
{
    public function __construct(
        private readonly ResultFactory $resultFactory
    ) {
    }

    public function execute(): ResultInterface
    {
        return $this->resultFactory->create(
            ResultFactory::TYPE_PAGE
        );
    }
}
