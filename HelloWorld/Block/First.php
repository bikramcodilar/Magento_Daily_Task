<?php
declare(strict_types=1);
namespace Codilar\HelloWorld\Block;

use Magento\Framework\View\Element\Template;

class First extends Template
{
    public function getDescription(): string
    {
        return 'First Block';
    }
}
