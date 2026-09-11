<?php
declare(strict_types=1);
namespace Codilar\OrderItemCancellation\Service;

use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Item;

class CancellationValidator
{
    private const array ELIGIBLE_STATES = [
        Order::STATE_NEW,
        Order::STATE_PENDING_PAYMENT,
        Order::STATE_PROCESSING,
    ];

    /**
     * @param Order $order
     * @param int $customerId
     * @return void
     * @throws LocalizedException
     */
    public function validateOrder(Order $order, int $customerId): void
    {
        if (!$order->getId()) {
            throw new LocalizedException(
                __('The order does not exist.')
            );
        }
        if ((int) $order->getCustomerId() !== $customerId) {
            throw new LocalizedException(
                __('You are not allowed to cancel this order.')
            );
        }
        if (!$this->isOrderEligible($order)) {
            throw new LocalizedException(
                __('This order cannot be cancelled.')
            );
        }
    }

    /**
     * @param Item $orderItem
     * @param float $requestedQty
     * @return void
     * @throws LocalizedException
     */
    public function validateItem(Item $orderItem, float $requestedQty): void
    {
        if (!$orderItem->getId()) {
            throw new LocalizedException(
                __('The selected order item does not exist.')
            );
        }
        if ($orderItem->getParentItemId() || !in_array($orderItem->getProductType(), ['simple', 'virtual'], true)) {
            throw new LocalizedException(
                __('This product cannot be cancelled.')
            );
        }
        if ($requestedQty <= 0) {
            throw new LocalizedException(
                __('Cancellation quantity must be greater than zero.')
            );
        }
        $qtyToCancel = (float) $orderItem->getQtyToCancel();
        if ($requestedQty > $qtyToCancel) {
            throw new LocalizedException(
                __('You can cancel a maximum of %1 for product %2.', $qtyToCancel, $orderItem->getSku())
            );
        }
    }

    /**
     * @param Order $order
     * @return bool
     */
    public function isOrderEligible(Order $order): bool
    {
        return in_array($order->getState(), self::ELIGIBLE_STATES, true);
    }
}
