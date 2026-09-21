<?php
declare(strict_types=1);
namespace Codilar\GiftBox\Model\Category;

use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\Exception\LocalizedException;
class CategoryResolver
{
    public function __construct(
        private readonly CollectionFactory $categoryCollectionFactory
    ) {
    }

    /**
     * @param int $categoryId
     * @return array
     * @throws LocalizedException
     */
    public function getCategoryIds(int $categoryId): array
    {
        if ($categoryId <= 0) {
            return [];
        }
        $categoryCollection = $this->categoryCollectionFactory->create();
        $categoryCollection->addAttributeToSelect([
            'entity_id',
            'path',
            'level',
        ]);
        $category = $categoryCollection
            ->addFieldToFilter('entity_id', $categoryId)
            ->getFirstItem();
        if (!$category->getId()) {
            return [];
        }
        $categoryPath = $category->getPath();
        $categoryIds = [];
        foreach ($categoryCollection as $currentCategory) {
            $currentPath = (string) $currentCategory->getPath();
            if (
                $currentPath === $categoryPath
                || str_starts_with(
                    $currentPath,
                    $categoryPath . '/'
                )
            ) {
                $categoryIds[] = (int) $currentCategory->getId();
            }
        }
        return $categoryIds;
    }
}
