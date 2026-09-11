<?php
namespace Codilar\HelloWorld\Api;

use Codilar\HelloWorld\Api\Data\EmployeeInterface;

interface EmployeeRepositoryInterface
{
    /**
     * @param int $id
     * @return \Codilar\HelloWorld\Api\Data\EmployeeInterface
     */
    public function getById(int $id): \Codilar\HelloWorld\Api\Data\EmployeeInterface;

    /**
     * @param \Codilar\HelloWorld\Api\Data\EmployeeInterface $employee
     * @return \Codilar\HelloWorld\Api\Data\EmployeeInterface
     */
    public function save(\Codilar\HelloWorld\Api\Data\EmployeeInterface $employee): \Codilar\HelloWorld\Api\Data\EmployeeInterface;

    /**
     * @param \Codilar\HelloWorld\Api\Data\EmployeeInterface $employee
     * @return bool
     */
    public function delete(\Codilar\HelloWorld\Api\Data\EmployeeInterface $employee): bool;
}
