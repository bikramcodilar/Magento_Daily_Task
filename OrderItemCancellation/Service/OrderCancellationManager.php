<?php

namespace Codilar\OrderItemCancellation\Service;

use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Model\Order;

class OrderCancellationManager
{
    public function applyItemCancellation(
        OrderItemInterface $item,
        float $qty,
        array $amounts
    ): void {
        $item->setQtyCanceled(
            (float) $item->getQtyCanceled() + $qty
        );
        $item->setTaxCanceled(
            (float) $item->getTaxCanceled()
            + $amounts['tax_amount']
        );
        $item->setDiscountTaxCompensationCanceled(
            (float) $item->getDiscountTaxCompensationCanceled()
            + $amounts['discount_tax_compensation_amount']
        );
    }

    public function applyOrderCancellation(
        Order $order,
        array $amounts
    ): void {
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
}
