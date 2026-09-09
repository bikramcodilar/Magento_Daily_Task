<?php
declare(strict_types=1);

namespace Codilar\OrderItemCancellation\Service;

use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Item;

class OrderCancellationManager
{
    /**
     * @param array<string, float> $amounts
     */
    public function applyItemCancellation(Item $orderItem, float $qty, array $amounts): void
    {
        $orderItem->setQtyCanceled(
            (float) $orderItem->getQtyCanceled() + $qty
        );
        $orderItem->setTaxCanceled(
            (float) $orderItem->getTaxCanceled()
            + $amounts['tax_amount']
        );
        $orderItem->setDiscountTaxCompensationCanceled(
            (float) $orderItem->getDiscountTaxCompensationCanceled()
            + $amounts['discount_tax_compensation_amount']
        );
    }

    /**
     * @param array<string, float> $amounts
     */
    public function applyOrderCancellation(Order $order, array $amounts): void
    {
        $order->setSubtotalCanceled(
            (float) $order->getSubtotalCanceled()
            + $amounts['row_total']
        );
        $order->setBaseSubtotalCanceled(
            (float) $order->getBaseSubtotalCanceled()
            + $amounts['base_row_total']
        );
        $order->setTaxCanceled(
            (float) $order->getTaxCanceled()
            + $amounts['tax_amount']
        );
        $order->setBaseTaxCanceled(
            (float) $order->getBaseTaxCanceled()
            + $amounts['base_tax_amount']
        );
        $order->setDiscountCanceled(
            (float) $order->getDiscountCanceled()
            + $amounts['discount_amount']
        );
        $order->setBaseDiscountCanceled(
            (float) $order->getBaseDiscountCanceled()
            + $amounts['base_discount_amount']
        );
        $order->setTotalCanceled(
            (float) $order->getTotalCanceled()
            + $amounts['grand_total']
        );
        $order->setBaseTotalCanceled(
            (float) $order->getBaseTotalCanceled()
            + $amounts['base_grand_total']
        );
    }

    public function isEntireOrderCancelled(Order $order): bool
    {
        foreach ($order->getAllItems() as $item) {
            if ($item->getParentItemId() || !in_array($item->getProductType(), ['simple', 'virtual'], true)) {
                continue;
            }
            $qtyToCancel = (float) $item->getQtyToCancel();
            if ($qtyToCancel > 0) {
                return false;
            }
            if ((float) $item->getQtyInvoiced() > 0 || (float) $item->getQtyShipped() > 0) {
                return false;
            }
        }
        return true;
    }

    //    public function finalizeOrder(Order $order): void
    //    {
    //        $order->setState(Order::STATE_CANCELED);
    //        $order->setStatus($order->getConfig()->getStateDefaultStatus(
    //            Order::STATE_CANCELED
    //        ));
    //    }
}
