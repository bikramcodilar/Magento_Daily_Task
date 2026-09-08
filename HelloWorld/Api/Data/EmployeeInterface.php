<?php
namespace Codilar\HelloWorld\Api\Data;
interface EmployeeInterface
{
    public function getId();
    public function setId($id);
    public function getName();
    public function setName($name);
    public function getEmail();
    public function setEmail($email);
}
