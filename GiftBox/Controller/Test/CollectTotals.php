<?php

declare(strict_types=1);

namespace Codilar\GiftBox\Controller\Test;

use Magento\Checkout\Model\Session as CheckoutSession;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class CollectTotals implements ActionInterface
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
     * @throws \Exception
     */
    public function execute(): Json
    {
        $quote = $this->checkoutSession->getQuote();
        $quote->collectTotals();
        $quote->save();
        $items = [];

        foreach ($quote->getAllVisibleItems() as $item) {
            $items[] = [
                'item_id' => $item->getItemId(),
                'product_id' => $item->getProductId(),
                'sku' => $item->getSku(),
                'qty' => $item->getQty(),
                'price' => $item->getPrice(),
                'custom_price' => $item->getCustomPrice(),
                'original_custom_price' => $item->getOriginalCustomPrice(),
                'row_total' => $item->getRowTotal(),
            ];
        }

        $result = $this->resultJsonFactory->create();

        return $result->setData([
            'message' => 'Quote totals collected successfully.',
            'quote_id' => $quote->getId(),
            'items' => $items,
        ]);
    }
}
