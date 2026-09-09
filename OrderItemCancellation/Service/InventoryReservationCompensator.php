<?php
declare(strict_types=1);

namespace Codilar\OrderItemCancellation\Service;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\InventorySalesApi\Api\Data\ItemToSellInterfaceFactory;
use Magento\InventorySalesApi\Api\Data\SalesChannelInterface;
use Magento\InventorySalesApi\Api\Data\SalesChannelInterfaceFactory;
use Magento\InventorySalesApi\Api\Data\SalesEventExtensionFactory;
use Magento\InventorySalesApi\Api\Data\SalesEventInterface;
use Magento\InventorySalesApi\Api\Data\SalesEventInterfaceFactory;
use Magento\InventorySalesApi\Api\PlaceReservationsForSalesEventInterface;
use Magento\Sales\Model\Order\Item;
use Magento\Store\Api\WebsiteRepositoryInterface;

class InventoryReservationCompensator
{
    public function __construct(
        private readonly ItemToSellInterfaceFactory $itemToSellFactory,
        private readonly PlaceReservationsForSalesEventInterface $placeReservationsForSalesEvent,
        private readonly SalesChannelInterfaceFactory $salesChannelFactory,
        private readonly WebsiteRepositoryInterface $websiteRepository,
        private readonly SalesEventInterfaceFactory $salesEventFactory,
        private readonly SalesEventExtensionFactory $salesEventExtensionFactory
    ) {
    }

    /**
     * Compensate MSI reservation for cancelled quantity.
     *
     * @throws NoSuchEntityException
     * @throws CouldNotSaveException
     * @throws LocalizedException
     * @throws InputException
     */
    public function compensate(
        Item $orderItem,
        float $quantity
    ): void {
        if ($quantity <= 0 || !$orderItem->getProductId()) {
            return;
        }

        $website = $this->websiteRepository->getById(
            (int) $orderItem->getStore()->getWebsiteId()
        );

        $salesChannel = $this->salesChannelFactory->create([
            'data' => [
                'type' => SalesChannelInterface::TYPE_WEBSITE,
                'code' => $website->getCode(),
            ],
        ]);

        $salesEventExtension = $this->salesEventExtensionFactory->create([
            'data' => [
                'objectIncrementId' => (string) $orderItem
                    ->getOrder()
                    ->getIncrementId(),
            ],
        ]);

        $salesEvent = $this->salesEventFactory->create([
            'type' => SalesEventInterface::EVENT_ORDER_CANCELED,
            'objectType' => SalesEventInterface::OBJECT_TYPE_ORDER,
            'objectId' => (string) $orderItem->getOrderId(),
        ]);

        $salesEvent->setExtensionAttributes(
            $salesEventExtension
        );

        $itemToSell = $this->itemToSellFactory->create([
            'sku' => (string) $orderItem->getSku(),
            'qty' => $quantity,
        ]);

        $this->placeReservationsForSalesEvent->execute(
            [$itemToSell],
            $salesChannel,
            $salesEvent
        );
    }
}
