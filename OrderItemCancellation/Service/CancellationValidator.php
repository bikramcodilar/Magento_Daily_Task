<?php
namespace Codilar\OrderItemCancellation\Service;

use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Model\Order;

class CancellationValidator
{
    /**
     * @param OrderInterface $order
     * @param int $customerId
     * @return void
     * @throws LocalizedException
     */
    public function validateOrder(
        OrderInterface $order,
        int $customerId
    ): void {
        if (!$order->getId()) {
            throw new LocalizedException(
                __('Order does not exist.')
            );
        }
        if ((int) $order->getCustomerId() !== $customerId) {
            throw new LocalizedException(
                __('You are not allowed to cancel this order.')
            );
        }
        if (!in_array(
            $order->getState(),
            [
                Order::STATE_NEW,
                Order::STATE_PENDING_PAYMENT,
                Order::STATE_PROCESSING,
            ],
            true
        )) {
            throw new LocalizedException(
                __('This order is not eligible for cancellation.')
            );
        }
    }

    /**
     * @param OrderItemInterface $item
     * @param float $requestedQty
     * @return void
     * @throws LocalizedException
     */
    public function validateItem(
        OrderItemInterface $item,
        float $requestedQty
    ): void {
        if (!$item->getId()) {
            throw new LocalizedException(
                __('Order item does not exist.')
            );
        }
        if ($item->getParentItemId()) {
            throw new LocalizedException(
                __('Parent order items cannot be cancelled directly.')
            );
        }
        if (!in_array(
            $item->getProductType(),
            ['simple', 'virtual'],
            true
        )) {
            throw new LocalizedException(
                __('This product type cannot be cancelled.')
            );
        }
        if ($requestedQty <= 0) {
            throw new LocalizedException(
                __('Cancellation quantity must be greater than zero.')
            );
        }
        if ($requestedQty > (float) $item->getQtyToCancel()) {
            throw new LocalizedException(
                __('You cannot cancel more than the available quantity.')
            );
        }
    }
}
