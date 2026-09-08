<?php

namespace Codilar\HelloWorld\Controller\ModelTest;

use Codilar\HelloWorld\Model\ResourceModel\Employee\CollectionFactory;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;

class Collection implements ActionInterface
{
    public function __construct(
        private readonly JsonFactory $jsonFactory,
        private readonly CollectionFactory $collectionFactory
    ) {
    }

    public function execute(): ResultInterface
    {
        $collection = $this->collectionFactory->create();
        $employees = [];
        //        $collection->addFieldToFilter(
        //            'email',
        //            'bikram'
        //        );
        foreach ($collection as $employee) {
            $employees[] = [
                'id' => $employee->getId(),
                'name' => $employee->getData('name'),
                'email' => $employee->getData('email')
            ];
        }
        $result = $this->jsonFactory->create();
        return $result->setData([
            'employees' => $employees
        ]);
    }
}
