<?php
declare(strict_types=1);

namespace Codilar\OrderItemCancellation\Service;

use Magento\Sales\Model\Order\Item;

class CancellationCalculator
{
    /**
     * Calculate financial amounts for cancelled quantity.
     *
     * @return array<string, float>
     */
    public function calculate(
        Item $orderItem,
        float $requestedQty
    ): array {
        $orderedQty = (float) $orderItem->getQtyOrdered();
        if ($orderedQty <= 0) {
            return [
                'row_total' => 0.0,
                'base_row_total' => 0.0,
                'tax_amount' => 0.0,
                'base_tax_amount' => 0.0,
                'discount_amount' => 0.0,
                'base_discount_amount' => 0.0,
                'grand_total' => 0.0,
                'base_grand_total' => 0.0,
                'discount_tax_compensation_amount' => 0.0,
                'base_discount_tax_compensation_amount' => 0.0,
            ];
        }
        $factor = $requestedQty / $orderedQty;
        $rowTotal = $this->proRate((float) $orderItem->getRowTotal(), $factor);
        $baseRowTotal = $this->proRate((float) $orderItem->getBaseRowTotal(), $factor);
        $taxAmount = $this->proRate((float) $orderItem->getTaxAmount(), $factor);
        $baseTaxAmount = $this->proRate((float) $orderItem->getBaseTaxAmount(), $factor);
        $discountAmount = $this->proRate((float) $orderItem->getDiscountAmount(), $factor);
        $baseDiscountAmount = $this->proRate((float) $orderItem->getBaseDiscountAmount(), $factor);
        $discountTaxCompensation = $this->proRate((float) $orderItem->getDiscountTaxCompensationAmount(), $factor);
        $baseDiscountTaxCompensation = $this->proRate((float) $orderItem->getBaseDiscountTaxCompensationAmount(), $factor);
        $grandTotal = $this->proRate((float) $orderItem->getRowTotal() + (float) $orderItem->getTaxAmount() + (float) $orderItem->getDiscountTaxCompensationAmount() - (float) $orderItem->getDiscountAmount(), $factor);
        $baseGrandTotal = $this->proRate((float) $orderItem->getBaseRowTotal() + (float) $orderItem->getBaseTaxAmount() + (float) $orderItem->getBaseDiscountTaxCompensationAmount() - (float) $orderItem->getBaseDiscountAmount(), $factor);
        return [
            'row_total' => $rowTotal,
            'base_row_total' => $baseRowTotal,
            'tax_amount' => $taxAmount,
            'base_tax_amount' => $baseTaxAmount,
            'discount_amount' => $discountAmount,
            'base_discount_amount' => $baseDiscountAmount,
            'discount_tax_compensation_amount' => $discountTaxCompensation,
            'base_discount_tax_compensation_amount' => $baseDiscountTaxCompensation,
            'grand_total' => $grandTotal,
            'base_grand_total' => $baseGrandTotal,
        ];
    }
    private function proRate(float $amount, float $factor): float
    {
        return round(
            $amount * $factor,
            4
        );
    }
}
