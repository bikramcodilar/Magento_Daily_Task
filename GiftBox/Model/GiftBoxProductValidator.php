<?php
declare(strict_types=1);

namespace Codilar\GiftBox\Model;

use Codilar\GiftBox\Model\Category\CategoryResolver;
use Codilar\GiftBox\Model\Inventory\SalabilityChecker;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\ResourceModel\Product as ProductResource;
class GiftBoxProductValidator
{
    public function __construct(
        private readonly ProductRepositoryInterface $productRepository,
        private readonly CategoryResolver $categoryResolver,
        private readonly SalabilityChecker $salabilityChecker,
        private readonly StoreManagerInterface $storeManager,
        private readonly ProductResource $productResource
    ) {
    }

    /**
     * @param string $sku
     * @param int $categoryId
     * @param float $quantity
     * @return ProductInterface
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function validate(
        string $sku,
        int $categoryId,
        float $quantity = 1.0
    ): ProductInterface {
        $product = $this->productRepository->get(
            $sku,
            false,
            $this->storeManager->getStore()->getId()
        );
        $this->validateProductStatus($product);
        $this->validateWebsiteAssignment($product);
        $this->validateCategory($product, $categoryId);
        $this->validateSalability($product->getSku(), $quantity);
        return $product;
    }

    /**
     * @param ProductInterface $product
     * @return void
     * @throws LocalizedException
     */
    private function validateProductStatus(
        ProductInterface $product
    ): void {
        if ((int) $product->getStatus() !== Status::STATUS_ENABLED) {
            throw new LocalizedException(
                __('Product "%1" is disabled.', $product->getSku())
            );
        }
    }

    /**
     * @param ProductInterface $product
     * @return void
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    private function validateWebsiteAssignment(
        ProductInterface $product
    ): void {
        $websiteId = (int) $this->storeManager
            ->getStore()
            ->getWebsiteId();
        $websiteIds = $this->productResource->getWebsiteIds(
            (int) $product->getId()
        );
        if (!in_array(
            $websiteId,
            array_map('intval', $websiteIds),
            true
        )) {
            throw new LocalizedException(
                __(
                    'Product "%1" is not available on this website.',
                    $product->getSku()
                )
            );
        }
    }

    /**
     * @param ProductInterface $product
     * @param int $configuredCategoryId
     * @return void
     * @throws LocalizedException
     */
    private function validateCategory(
        ProductInterface $product,
        int $configuredCategoryId
    ): void {
        $eligibleCategoryIds = $this->categoryResolver
            ->getCategoryIds($configuredCategoryId);
        $productCategoryIds = array_map(
            'intval',
            $product->getCategoryIds()
        );
        if (
            !array_intersect(
                $eligibleCategoryIds,
                $productCategoryIds
            )
        ) {
            throw new LocalizedException(
                __('Product "%1" does not belong to the configured Gift Box category.', $product->getSku())
            );
        }
    }

    /**
     * @param string $sku
     * @param float $quantity
     * @return void
     * @throws LocalizedException
     */
    private function validateSalability(
        string $sku,
        float $quantity
    ): void {
        if (!$this->salabilityChecker->isSalable($sku, $quantity)) {
            throw new LocalizedException(
                __('Product "%1" is currently unavailable.', $sku)
            );
        }
    }
}
