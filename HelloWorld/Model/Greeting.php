<?php
namespace Codilar\HelloWorld\Model;

use Codilar\HelloWorld\Api\GreetingInterface;

class Greeting implements GreetingInterface
{
    public function getMessage(string $name): string
    {
        return "Hello $name";
    }
}
