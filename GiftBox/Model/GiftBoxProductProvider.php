<?php

declare(strict_types=1);

namespace Codilar\GiftBox\Model;

use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Codilar\GiftBox\Model\Category\CategoryResolver;
use Magento\Framework\Exception\LocalizedException;

class GiftBoxProductProvider
{
    public function __construct(
        private readonly CollectionFactory $productCollectionFactory,
        private readonly Config $config,
        private readonly CategoryResolver $categoryResolver
    ) {
    }

    /**
     * @return Collection
     * @throws LocalizedException
     */
    public function getEligibleCoffeeProducts(): Collection
    {
        return $this->getEligibleProducts(
            $this->config->getCoffeeCategoryId()
        );
    }

    /**
     * @return Collection
     * @throws LocalizedException
     */
    public function getEligibleEquipmentProducts(): Collection
    {
        return $this->getEligibleProducts(
            $this->config->getEquipmentCategoryId()
        );
    }

    /**
     * @param int $categoryId
     * @return Collection
     * @throws LocalizedException
     */
    private function getEligibleProducts(int $categoryId): Collection
    {
        $collection = $this->productCollectionFactory->create();
        $categoryIds = $this->categoryResolver->getCategoryIds(
            $categoryId
        );
        if ($categoryIds === []) {
            return $collection;
        }
        $collection->addAttributeToSelect([
            'name',
            'price',
            'weight',
            'short_description',
            'image',
            'small_image',
            'thumbnail',
        ]);
        $collection->addCategoriesFilter([
            'in' => $categoryIds,
        ]);
        $collection->addAttributeToFilter(
            'status',
            Status::STATUS_ENABLED
        );
        $collection->setVisibility([
            Visibility::VISIBILITY_IN_CATALOG,
            Visibility::VISIBILITY_IN_SEARCH,
            Visibility::VISIBILITY_BOTH,
        ]);
        $collection->addAttributeToSort(
            'name',
            'ASC'
        );
        return $collection;
    }
}
