<?php

namespace Codilar\HelloWorld\Controller\ModelTest;

use Codilar\HelloWorld\Model\Employee;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;

class Index implements ActionInterface
{
    public function __construct(
        private readonly JsonFactory $jsonFactory,
        private readonly Employee $employee
    ) {
    }

    public function execute(): ResultInterface
    {
        $this->employee->setId(101);
        $this->employee->setName('Bikram');
        $this->employee->setEmail('bikram@gmail.com');

        $result = $this->jsonFactory->create();

        return $result->setData([
            'id' => $this->employee->getId(),
            'name' => $this->employee->getName(),
            'email' => $this->employee->getEmail()
        ]);
    }
}
