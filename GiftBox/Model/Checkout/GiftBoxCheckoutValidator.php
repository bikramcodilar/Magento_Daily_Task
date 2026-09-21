<?php

declare(strict_types=1);

namespace Codilar\GiftBox\Model\Checkout;

use Codilar\GiftBox\Model\Inventory\SalabilityChecker;
use Codilar\GiftBox\Model\Quote\GiftBoxQuoteDataReader;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote;
use Magento\Quote\Model\Quote\Item;
use Magento\Store\Model\StoreManagerInterface;

class GiftBoxCheckoutValidator
{
    public function __construct(
        private readonly GiftBoxQuoteDataReader $giftBoxQuoteDataReader,
        private readonly ProductRepositoryInterface $productRepository,
        private readonly ProductResource $productResource,
        private readonly StoreManagerInterface $storeManager,
        private readonly SalabilityChecker $salabilityChecker
    ) {
    }

    /**
     * @param Quote $quote
     * @return void
     * @throws LocalizedException
     */
    public function validateQuote(Quote $quote): void
    {
        foreach ($quote->getAllVisibleItems() as $item) {
            if (!$this->isGiftBoxItem($item)) {
                continue;
            }

            $this->validateGiftBoxItem($item);
        }
    }

    /**
     * @param Item $item
     * @return bool
     */
    private function isGiftBoxItem(Item $item): bool
    {
        return (bool) $item->getData('giftbox_data');
    }

    /**
     * @param Item $item
     * @return void
     * @throws LocalizedException
     */
    private function validateGiftBoxItem(Item $item): void
    {
        $giftBoxData = $this->giftBoxQuoteDataReader->get($item);

        $coffeeSkus = $giftBoxData['coffee_skus'] ?? [];
        $equipmentSku = $giftBoxData['equipment_sku'] ?? null;

        foreach ($coffeeSkus as $sku) {
            $this->validateComponent((string) $sku);
        }

        if ($equipmentSku) {
            $this->validateComponent((string) $equipmentSku);
        }
    }

    /**
     * @param string $sku
     * @return void
     * @throws LocalizedException
     */
    private function validateComponent(string $sku): void
    {
        try {
            $product = $this->productRepository->get(
                $sku,
                false,
                $this->storeManager->getStore()->getId()
            );
        } catch (NoSuchEntityException) {
            throw new LocalizedException(
                __(
                    'Gift Box cannot be purchased because product "%1" no longer exists.',
                    $sku
                )
            );
        }
        $this->validateProductStatus(
            $product->getStatus(),
            $sku
        );
        $this->validateWebsiteAssignment(
            (int) $product->getId(),
            $sku
        );
        $this->validateSalability($sku);
    }

    /**
     * @param mixed $status
     * @param string $sku
     * @return void
     * @throws LocalizedException
     */
    private function validateProductStatus(
        mixed $status,
        string $sku
    ): void {
        if ((int) $status !== Status::STATUS_ENABLED) {
            throw new LocalizedException(
                __(
                    'Gift Box cannot be purchased because product "%1" is disabled.',
                    $sku
                )
            );
        }
    }

    /**
     * @param int $productId
     * @param string $sku
     * @return void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function validateWebsiteAssignment(
        int $productId,
        string $sku
    ): void {
        $websiteId = (int) $this->storeManager
            ->getStore()
            ->getWebsiteId();

        $websiteIds = $this->productResource->getWebsiteIds(
            $productId
        );

        if (!in_array(
            $websiteId,
            array_map('intval', $websiteIds),
            true
        )) {
            throw new LocalizedException(
                __(
                    'Gift Box cannot be purchased because product "%1" is not available on this website.',
                    $sku
                )
            );
        }
    }

    /**
     * @param string $sku
     * @return void
     * @throws LocalizedException
     */
    private function validateSalability(string $sku): void
    {
        if (!$this->salabilityChecker->isSalable($sku, 1.0)) {
            throw new LocalizedException(
                __(
                    'Gift Box cannot be purchased because product "%1" is currently unavailable.',
                    $sku
                )
            );
        }
    }
}
