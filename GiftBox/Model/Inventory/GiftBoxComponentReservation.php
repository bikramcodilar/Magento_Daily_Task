<?php

declare(strict_types=1);

namespace Codilar\GiftBox\Model\Inventory;

use Codilar\GiftBox\Model\Quote\GiftBoxQuoteDataReader;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderItemInterface;

class GiftBoxComponentReservation
{
    public function __construct(
        private readonly GiftBoxQuoteDataReader $giftBoxQuoteDataReader
    ) {
    }

    /**
     * @param OrderInterface $order
     * @return array
     */
    public function getComponentsForOrder(
        OrderInterface $order
    ): array {
        $components = [];

        foreach ($order->getItems() as $item) {
            if (!$this->isGiftBoxItem($item)) {
                continue;
            }

            $giftBoxData = $this->giftBoxQuoteDataReader->get($item);

            $coffeeSkus = $giftBoxData['coffee_skus'] ?? [];

            foreach ($coffeeSkus as $sku) {
                $sku = (string) $sku;

                if ($sku === '') {
                    continue;
                }

                $components[$sku] =
                    ($components[$sku] ?? 0.0) + 1.0;
            }

            $equipmentSku = $giftBoxData['equipment_sku'] ?? null;

            if ($equipmentSku) {
                $equipmentSku = (string) $equipmentSku;

                if ($equipmentSku !== '') {
                    $components[$equipmentSku] =
                        ($components[$equipmentSku] ?? 0.0) + 1.0;
                }
            }
        }

        return $components;
    }

    /**
     * @param OrderItemInterface $item
     * @return bool
     */
    private function isGiftBoxItem(
        OrderItemInterface $item
    ): bool {
        return (bool) $item->getData('giftbox_data');
    }
}
