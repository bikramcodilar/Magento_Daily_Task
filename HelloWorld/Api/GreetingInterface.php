<?php
namespace Codilar\HelloWorld\Api;

interface GreetingInterface
{
    public function getMessage(string $name): string;
}
