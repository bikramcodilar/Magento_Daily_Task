<?php

declare(strict_types=1);

namespace Codilar\GiftBox\Plugin\Inventory;

use Codilar\GiftBox\Model\Inventory\GiftBoxComponentReservation;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\InventorySalesApi\Api\Data\ItemToSellInterface;
use Magento\InventorySalesApi\Api\Data\ItemToSellInterfaceFactory;
use Magento\InventorySalesApi\Api\Data\SalesChannelInterface;
use Magento\InventorySalesApi\Api\Data\SalesEventInterface;
use Magento\InventorySalesApi\Api\PlaceReservationsForSalesEventInterface;

class PlaceGiftBoxReservations
{
    public function __construct(
        private readonly GiftBoxComponentReservation $giftBoxComponentReservation,
        private readonly ItemToSellInterfaceFactory $itemToSellFactory
    ) {
    }

    /**
     * @param PlaceReservationsForSalesEventInterface $subject
     * @param array $items
     * @param SalesChannelInterface $salesChannel
     * @param SalesEventInterface $salesEvent
     * @return array
     * @throws NoSuchEntityException
     */
    public function beforeExecute(
        PlaceReservationsForSalesEventInterface $subject,
        array $items,
        SalesChannelInterface $salesChannel,
        SalesEventInterface $salesEvent
    ): array {
        if (
            $salesEvent->getType()
            !== SalesEventInterface::EVENT_ORDER_PLACED
        ) {
            return [
                $items,
                $salesChannel,
                $salesEvent,
            ];
        }
        $orderId = (int) $salesEvent->getObjectId();
        if ($orderId <= 0) {
            return [
                $items,
                $salesChannel,
                $salesEvent,
            ];
        }
        $components =
            $this->giftBoxComponentReservation
                ->getComponentsForOrder($orderId);
        foreach ($components as $sku => $quantity) {
            $items[] = $this->itemToSellFactory->create([
                'sku' => $sku,
                'qty' => $quantity,
            ]);
        }
        return [
            $items,
            $salesChannel,
            $salesEvent,
        ];
    }
}
