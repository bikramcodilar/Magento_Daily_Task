<?php

namespace Codilar\HelloWorld\Controller\ModelTest;

use Codilar\HelloWorld\Api\EmployeeRepositoryInterface;
use Codilar\HelloWorld\Model\EmployeeFactory;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;

class Create implements ActionInterface
{
    public function __construct(
        private readonly JsonFactory $jsonFactory,
        private readonly EmployeeFactory $employeeFactory,
        private readonly EmployeeRepositoryInterface $employeeRepository
    ) {
    }

    public function execute(): ResultInterface
    {
        $employees = [
            [
                'name' => 'Rahul',
                'email' => 'rahul@example.com'
            ],
            [
                'name' => 'Amit',
                'email' => 'amit@example.com'
            ],
            [
                'name' => 'Sourav',
                'email' => 'sourav@example.com'
            ]
        ];
        foreach ($employees as $data) {
            $employee = $this->employeeFactory->create();
            $employee->setData($data);
            $this->employeeRepository->save($employee);
        }
        $result = $this->jsonFactory->create();
        return $result->setData([
            'message' => 'Employees created successfully'
        ]);
    }
}
