<?php
namespace Codilar\LoginActivity\Observer;

use Codilar\LoginActivity\Logger\Logger;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class CustomerLogin implements ObserverInterface
{
    /**
     * @param Logger $logger
     */
    public function __construct(
        private readonly Logger $logger
    ) {
    }
    /**
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer): void
    {
        $customer = $observer->getEvent()->getCustomer();
        $customerId = $customer->getId();
        $email = $customer->getEmail();
        $this->logger->info(
            'Customer Login',
            [
                'customer_id' => $customerId,
                'email' => $email
            ]
        );
    }
}
