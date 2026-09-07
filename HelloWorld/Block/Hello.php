<?php
declare(strict_types=1);
namespace Codilar\HelloWorld\Block;

use Magento\Framework\View\Element\Template;
use Magento\Customer\Model\Session;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
class Hello extends Template
{
    public function __construct(
        Template\Context $context,
        private readonly Session $customerSession,
        private readonly TimezoneInterface $timezone
    ) {
        parent::__construct($context);
    }
    public function getDescription(): string
    {
        return 'Welcome to my first custom Magento module.';
    }
    public function getCurrentTime(): string
    {
        return $this->timezone->date()->format('H:i:s');
    }
    public function getCustomerName(): string
    {
        if (!$this->customerSession->isLoggedIn()) {
            return 'Guest';
        }
        return $this->customerSession->getCustomer()->getName();
    }
}
