<?php

declare(strict_types=1);

namespace Codilar\GiftBox\Plugin\Inventory;

use Codilar\GiftBox\Model\Inventory\GiftBoxComponentReservation;
use Magento\InventorySales\Model\AppendReservations;
use Magento\InventorySalesApi\Api\Data\ItemToSellInterfaceFactory;
use Magento\Sales\Api\Data\OrderInterface;

class AppendGiftBoxComponentReservationsPlugin
{
    public function __construct(
        private readonly GiftBoxComponentReservation $giftBoxComponentReservation,
        private readonly ItemToSellInterfaceFactory $itemToSellFactory
    ) {
    }

    /**
     * @param AppendReservations $subject
     * @param $websiteId
     * @param array $itemsBySku
     * @param OrderInterface $order
     * @param array $itemsToSell
     * @return array
     */
    public function beforeReserve(
        AppendReservations $subject,
        $websiteId,
        array $itemsBySku,
        OrderInterface $order,
        array $itemsToSell
    ): array {
        $components =
            $this->giftBoxComponentReservation
                ->getComponentsForOrder($order);

        foreach ($components as $sku => $quantity) {
            $itemsBySku[$sku] =
                ($itemsBySku[$sku] ?? 0.0) + $quantity;

            $itemsToSell[] = $this->itemToSellFactory->create([
                'sku' => $sku,
                'qty' => -$quantity,
            ]);
        }

        return [
            $websiteId,
            $itemsBySku,
            $order,
            $itemsToSell,
        ];
    }
}
