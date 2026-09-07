<?php
//namespace Codilar\LoginActivity\Observer;
//use Magento\Framework\Event\Observer;
//use Magento\Framework\Event\ObserverInterface;
//use Psr\Log\LoggerInterface;
//class CustomerLogin implements ObserverInterface
//{
//    public function __construct(private readonly LoggerInterface $logger)
//    {
//    }
//
//    public function execute(Observer $observer): void
//    {
//        $customer = $observer->getEvent()->getCustomer();
//        $customerId = $customer->getId();
//        $email = $customer->getEmail();
//        $this->logger->info(
//            'Customer login detected',
//            [
//                'customer_id' => $customerId,
//                'email' => $email
//            ]
//        );
//    }
//}

namespace Codilar\LoginActivity\Observer;

use Codilar\LoginActivity\Logger\Logger;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class CustomerLogin implements ObserverInterface
{
    public function __construct(
        private readonly Logger $logger
    ) {
    }

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
