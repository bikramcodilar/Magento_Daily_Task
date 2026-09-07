<?php
namespace Codilar\HelloWorld\Model;

use Codilar\HelloWorld\Api\MessageInterface;

class Message2 implements MessageInterface
{
    public function getMessage(): string
    {
        return 'Hello from the Message class 2!';
    }
}
