<?php

namespace Codilar\HelloWorld\Model\ResourceModel\Employee;

use Codilar\HelloWorld\Model\Employee;
use Codilar\HelloWorld\Model\ResourceModel\Employee as EmployeeResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    protected $_idFieldName = 'employee_id';

    /**
     * @return void
     */
    protected function _construct(): void
    {
        $this->_init(
            Employee::class,
            EmployeeResource::class
        );
    }
}
