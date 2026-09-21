<?php

declare(strict_types=1);

namespace Codilar\GiftBox\Plugin\Inventory;

use Codilar\GiftBox\Model\Quote\GiftBoxQuoteDataReader;
use Magento\InventorySales\Model\GetItemsToCancelFromOrderItem;
use Magento\InventorySalesApi\Api\Data\ItemToSellInterfaceFactory;
use Magento\Sales\Api\Data\OrderItemInterface;

class GetItemsToCancelFromOrderItemPlugin
{
    public function __construct(
        private readonly GiftBoxQuoteDataReader $giftBoxQuoteDataReader,
        private readonly ItemToSellInterfaceFactory $itemToSellFactory
    ) {
    }

    /**
     * @param GetItemsToCancelFromOrderItem $subject
     * @param array $result
     * @param OrderItemInterface $orderItem
     * @return array
     */
    public function afterExecute(
        GetItemsToCancelFromOrderItem $subject,
        array $result,
        OrderItemInterface $orderItem
    ): array {
        $giftBoxData = $this->giftBoxQuoteDataReader->get($orderItem);
        if ($giftBoxData === []) {
            return $result;
        }
        $components = [];
        foreach ($giftBoxData['coffee_skus'] ?? [] as $sku) {
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
        foreach ($components as $sku => $quantity) {
            $result[] = $this->itemToSellFactory->create([
                'sku' => $sku,
                'qty' => $quantity,
            ]);
        }
        return $result;
    }
}
