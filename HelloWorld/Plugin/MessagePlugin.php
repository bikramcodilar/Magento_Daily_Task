<?php
namespace Codilar\HelloWorld\Plugin;

use Codilar\HelloWorld\Model\Message;

class MessagePlugin
{
    public function afterGetMessage(
        Message $subject,
        string  $result
    ): string {
        return $result . ' Welcome to Codilar!';
    }
}
