<?php

namespace Codilar\HelloWorld\Model;

use Codilar\HelloWorld\Model\ResourceModel\Employee as EmployeeResource;
use Codilar\HelloWorld\Api\Data\EmployeeInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;

class Employee extends AbstractModel implements EmployeeInterface
{
    /**
     * @return void
     * @throws LocalizedException
     */
    protected function _construct(): void
    {
        $this->_init(EmployeeResource::class);
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->getData('employee_id');
    }

    /**
     * @param $id
     * @return Employee
     */
    public function setId($id): Employee
    {
        return $this->setData('employee_id', $id);
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->getData('name');
    }

    /**
     * @param $name
     * @return Employee
     */
    public function setName($name): Employee
    {
        return $this->setData('name', $name);
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->getData('email');
    }

    /**
     * @param $email
     * @return Employee
     */
    public function setEmail($email): Employee
    {
        return $this->setData('email', $email);
    }
}
