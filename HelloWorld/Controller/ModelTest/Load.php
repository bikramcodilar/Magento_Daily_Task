<?php

namespace Codilar\HelloWorld\Controller\ModelTest;

use Codilar\HelloWorld\Api\EmployeeRepositoryInterface;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;

class Load implements ActionInterface
{
    public function __construct(
        private readonly JsonFactory $jsonFactory,
        private readonly EmployeeRepositoryInterface $employeeRepository
    ) {
    }

    public function execute(): ResultInterface
    {
        $employee = $this->employeeRepository->getById(1);
        $result = $this->jsonFactory->create();
        return $result->setData([
            'id' => $employee->getId(),
            'name' => $employee->getName(),
            'email' => $employee->getEmail()
        ]);
    }
}
