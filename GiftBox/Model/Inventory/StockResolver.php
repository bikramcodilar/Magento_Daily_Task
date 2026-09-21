<?php
declare(strict_types=1);
namespace Codilar\GiftBox\Model\Inventory;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\InventorySalesApi\Api\Data\SalesChannelInterface;
use Magento\InventorySalesApi\Api\StockResolverInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\LocalizedException;

class StockResolver
{
    public function __construct(
        private readonly StockResolverInterface $stockResolver,
        private readonly StoreManagerInterface $storeManager
    ) {
    }

    /**
     * @return int
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function getCurrentStockId(): int
    {
        $websiteCode = $this->storeManager->getStore()->getWebsite()->getCode();
        $stock = $this->stockResolver->execute(
            SalesChannelInterface::TYPE_WEBSITE,
            $websiteCode
        );
        $stockId = (int) $stock->getStockId();
        if ($stockId <= 0) {
            throw new LocalizedException(
                __('Unable to resolve the inventory stock for the current website.')
            );
        }
        return $stockId;
    }
}
