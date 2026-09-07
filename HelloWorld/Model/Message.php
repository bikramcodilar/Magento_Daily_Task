<?php
namespace Codilar\HelloWorld\Model;

use Codilar\HelloWorld\Api\MessageInterface;

class Message implements MessageInterface
{
    public function getMessage(): string
    {
        return 'Hello from the Message class!';
    }
}
