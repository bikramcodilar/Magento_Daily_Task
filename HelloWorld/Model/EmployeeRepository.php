<?php
namespace Codilar\HelloWorld\Model;

use Codilar\HelloWorld\Api\Data\EmployeeInterface;
use Codilar\HelloWorld\Api\EmployeeRepositoryInterface;
use Codilar\HelloWorld\Model\ResourceModel\Employee as EmployeeResource;
use Codilar\HelloWorld\Model\ResourceModel\Employee\CollectionFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class EmployeeRepository implements EmployeeRepositoryInterface
{
    public function __construct(
        private readonly EmployeeFactory  $employeeFactory,
        private readonly EmployeeResource $employeeResource,
        private CollectionFactory         $collectionFactory
    ) {
    }

    /**
     * @param int $id
     * @return EmployeeInterface
     * @throws NoSuchEntityException
     */
    public function getById(int $id): \Codilar\HelloWorld\Api\Data\EmployeeInterface
    {
        $employee = $this->employeeFactory->create();
        $this->employeeResource->load(
            $employee,
            $id
        );
        if (!$employee->getId()) {
            throw new NoSuchEntityException(
                __('Employee with ID "%1" does not exist.', $id)
            );
        }
        return $employee;
    }

    /**
     * @param EmployeeInterface $employee
     * @return EmployeeInterface
     * @throws CouldNotSaveException
     */
    public function save(\Codilar\HelloWorld\Api\Data\EmployeeInterface $employee): \Codilar\HelloWorld\Api\Data\EmployeeInterface
    {
        try {
            $this->employeeResource->save($employee);
        } catch (\Exception $e) {
            throw new CouldNotSaveException(
                __('Could not save employee: %1', $e->getMessage()),
                $e
            );
        }
        return $employee;
    }

    /**
     * @param EmployeeInterface $employee
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(\Codilar\HelloWorld\Api\Data\EmployeeInterface $employee): bool
    {
        try {
            $this->employeeResource->delete($employee);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(
                __('Could not delete employee: %1', $e->getMessage()),
                $e
            );
        }
        return true;
    }
}
