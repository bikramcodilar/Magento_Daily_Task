<?php
declare(strict_types=1);
namespace Codilar\HelloWorld\Block;

use Magento\Framework\View\Element\Template;

class Second extends Template
{
    public function getDescription(): string
    {
        return 'Second Block';
    }
}
