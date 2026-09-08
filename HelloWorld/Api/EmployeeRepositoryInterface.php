<?php

namespace Codilar\HelloWorld\Api;

use Codilar\HelloWorld\Api\Data\EmployeeInterface;

interface EmployeeRepositoryInterface
{
    public function getById($id): EmployeeInterface;
    public function save(EmployeeInterface $employee): EmployeeInterface;
    public function delete(EmployeeInterface $employee): bool;
}
