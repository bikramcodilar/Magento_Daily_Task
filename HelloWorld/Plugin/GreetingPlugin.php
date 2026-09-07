<?php
namespace Codilar\HelloWorld\Plugin;

use Codilar\HelloWorld\Model\Greeting;

class GreetingPlugin
{
    public function beforeGetMessage(
        Greeting $subject,
        string  $name
    ): array {
        $name = strtoupper($name);
        return [$name];
    }
}
