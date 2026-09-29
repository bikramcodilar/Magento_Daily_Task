<?php

namespace Codilar\OrderItemCancellation\Service;

use Magento\Sales\Api\Data\OrderItemInterface;

class CancellationCalculator
{
    public function calculate(
        OrderItemInterface $item,
        float $requestedQty
    ): array {
        $orderedQty = (float) $item->getQtyOrdered();
        if ($orderedQty <= 0) {
            return $this->emptyAmounts();
        }
        $factor = $requestedQty / $orderedQty;
        $rowTotal = $this->proRate(
            (float) $item->getRowTotal(),
            $factor
        );

        $baseRowTotal = $this->proRate(
            (float) $item->getBaseRowTotal(),
            $factor
        );

        $taxAmount = $this->proRate(
            (float) $item->getTaxAmount(),
            $factor
        );
        $baseTaxAmount = $this->proRate(
            (float) $item->getBaseTaxAmount(),
            $factor
        );
        $discountAmount = $this->proRate(
            (float) $item->getDiscountAmount(),
            $factor
        );
        $baseDiscountAmount = $this->proRate(
            (float) $item->getBaseDiscountAmount(),
            $factor
        );
        $discountTaxCompensation = $this->proRate(
            (float) $item->getDiscountTaxCompensationAmount(),
            $factor
        );
        $baseDiscountTaxCompensation = $this->proRate(
            (float) $item->getBaseDiscountTaxCompensationAmount(),
            $factor
        );
        return [
            'row_total' => $rowTotal,
            'base_row_total' => $baseRowTotal,
            'tax_amount' => $taxAmount,
            'base_tax_amount' => $baseTaxAmount,
            'discount_amount' => $discountAmount,
            'base_discount_amount' => $baseDiscountAmount,
            'discount_tax_compensation_amount' => $discountTaxCompensation,
            'base_discount_tax_compensation_amount' => $baseDiscountTaxCompensation,
            'grand_total' => $rowTotal
                + $taxAmount
                + $discountTaxCompensation
                - $discountAmount,
            'base_grand_total' => $baseRowTotal
                + $baseTaxAmount
                + $baseDiscountTaxCompensation
                - $baseDiscountAmount,
        ];
    }

    private function proRate(float $amount, float $factor): float
    {
        return round($amount * $factor, 4);
    }

    private function emptyAmounts(): array
    {
        return [
            'row_total' => 0.0,
            'base_row_total' => 0.0,
            'tax_amount' => 0.0,
            'base_tax_amount' => 0.0,
            'discount_amount' => 0.0,
            'base_discount_amount' => 0.0,
            'discount_tax_compensation_amount' => 0.0,
            'base_discount_tax_compensation_amount' => 0.0,
            'grand_total' => 0.0,
            'base_grand_total' => 0.0,
        ];
    }
}
