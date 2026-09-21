<?php
declare(strict_types=1);
namespace Codilar\GiftBox\Model\Inventory;

use Magento\Framework\Exception\LocalizedException;
use Magento\InventorySalesApi\Api\IsProductSalableForRequestedQtyInterface;

class SalabilityChecker
{
    public function __construct(
        private readonly IsProductSalableForRequestedQtyInterface $isProductSalableForRequestedQty,
        private readonly StockResolver $stockResolver
    ) {
    }

    /**
     * @param string $sku
     * @param float $quantity
     * @return bool
     * @throws LocalizedException
     */
    public function isSalable(
        string $sku,
        float $quantity = 1.0
    ): bool {
        $stockId = $this->stockResolver->getCurrentStockId();
        $result = $this->isProductSalableForRequestedQty->execute(
            $sku,
            $stockId,
            $quantity
        );
        return $result->isSalable();
    }
}
