<?php

declare(strict_types=1);

namespace Codilar\ProductEnquiry\Model\Resolver;

use Codilar\ProductEnquiry\Model\ResourceModel\ProductEnquiry\CollectionFactory;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class ProductEnquiries implements ResolverInterface
{
    private const int DEFAULT_PAGE_SIZE = 20;
    private const int MAX_PAGE_SIZE = 100;
    public function __construct(
        private readonly CollectionFactory $collectionFactory
    ) {
    }

    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        ?array $value = null,
        ?array $args = null
    ): array {
        $args = $args ?? [];
        $currentPage = (int) ($args['currentPage'] ?? 1);
        $pageSize = (int) ($args['pageSize'] ?? self::DEFAULT_PAGE_SIZE);
        $sku = trim((string) ($args['sku'] ?? ''));
        if ($currentPage < 1) {
            throw new GraphQlInputException(
                __('currentPage must be at least 1.')
            );
        }
        if ($pageSize < 1 || $pageSize > self::MAX_PAGE_SIZE) {
            throw new GraphQlInputException(
                __('pageSize must be between 1 and 100.')
            );
        }
        $collection = $this->collectionFactory->create();
        if ($sku !== '') {
            $collection->addFieldToFilter('sku', $sku);
        }
        $totalCount = (int) $collection->getSize();
        $totalPages = (int) ceil($totalCount / $pageSize);
        $collection->setOrder('enquiry_id', 'DESC');
        $collection->setPageSize($pageSize);
        $collection->setCurPage($currentPage);
        $items = [];
        foreach ($collection as $enquiry) {
            $items[] = [
                'enquiry_id' => (int) $enquiry->getEnquiryId(),
                'name' => $enquiry->getName(),
                'email' => $enquiry->getEmail(),
                'sku' => $enquiry->getSku(),
                'quantity' => $enquiry->getQuantity(),
                'created_at' => (string) $enquiry->getData('created_at'),
            ];
        }
        return [
            'items' => $items,
            'total_count' => $totalCount,
            'page_info' => [
                'current_page' => $currentPage,
                'page_size' => $pageSize,
                'total_pages' => $totalPages,
            ],
        ];
    }
}
