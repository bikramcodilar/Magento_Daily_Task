<?php
declare(strict_types=1);
namespace Codilar\GiftBox\Model\Config\Source;

use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory;
use Magento\Framework\Data\OptionSourceInterface;
use Magento\Framework\Exception\LocalizedException;

class Category implements OptionSourceInterface
{
    public function __construct(
        private readonly CollectionFactory $categoryCollectionFactory
    ) {
    }

    /**
     * @return array[]
     * @throws LocalizedException
     */
    public function toOptionArray(): array
    {
        $options = [
            [
                'value' => '',
                'label' => __('-- Please Select --'),
            ],
        ];
        $collection = $this->categoryCollectionFactory->create();
        $collection->addAttributeToSelect([
            'name',
            'is_active',
            'parent_id',
        ]);
        $collection->addIsActiveFilter();
        $collection->setOrder('path', 'ASC');
        foreach ($collection as $category) {
            $options[] = [
                'value' => $category->getId(),
                'label' => $this->getCategoryLabel($category),
            ];
        }
        return $options;
    }

    /**
     * @param $category
     * @return string
     */
    private function getCategoryLabel($category): string
    {
        return (string) $category->getName();
    }
}
