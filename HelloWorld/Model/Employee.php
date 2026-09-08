<?php

namespace Codilar\HelloWorld\Model;

use Codilar\HelloWorld\Model\ResourceModel\Employee as EmployeeResource;
use Codilar\HelloWorld\Api\Data\EmployeeInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;

class Employee extends AbstractModel implements EmployeeInterface
{
    /**
     * @throws LocalizedException
     */
    protected function _construct(): void
    {
        $this->_init(EmployeeResource::class);
    }
    public function getId()
    {
        return $this->getData('employee_id');
    }

    public function setId($id): Employee
    {
        return $this->setData('employee_id', $id);
    }

    public function getName()
    {
        return $this->getData('name');
    }

    public function setName($name): Employee
    {
        return $this->setData('name', $name);
    }

    public function getEmail()
    {
        return $this->getData('email');
    }

    public function setEmail($email): Employee
    {
        return $this->setData('email', $email);
    }
}
