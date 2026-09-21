<?php

declare(strict_types=1);

namespace Codilar\GiftBox\Controller\Test;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class ReservationInfo implements ActionInterface
{
    public function __construct(
        private readonly CheckoutSession $checkoutSession,
        private readonly JsonFactory $resultJsonFactory
    ) {
    }

    /**
     * @return Json
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function execute(): Json
    {
        $quote = $this->checkoutSession->getQuote();
        $result = $this->resultJsonFactory->create();
        return $result->setData([
            'quote_id' => $quote->getId(),
            'items' => array_map(
                static function ($item): array {
                    return [
                        'item_id' => $item->getItemId(),
                        'product_id' => $item->getProductId(),
                        'sku' => $item->getSku(),
                        'qty' => $item->getQty(),
                        'giftbox_data' => $item->getData('giftbox_data'),
                    ];
                },
                $quote->getAllVisibleItems()
            ),
        ]);
    }
}
