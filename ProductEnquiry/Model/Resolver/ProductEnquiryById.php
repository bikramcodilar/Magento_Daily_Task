<?php

declare(strict_types=1);

namespace Codilar\ProductEnquiry\Model\Resolver;

use Codilar\ProductEnquiry\Api\ProductEnquiryRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlNoSuchEntityException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class ProductEnquiryById implements ResolverInterface
{
    public function __construct(
        private readonly ProductEnquiryRepositoryInterface $enquiryRepository
    ) {
    }

    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        ?array $value = null,
        ?array $args = null
    ): ?array {
        $enquiryId = (int) ($args['enquiry_id'] ?? 0);
        try {
            $enquiry = $this->enquiryRepository->getById($enquiryId);
        } catch (NoSuchEntityException $exception) {
            throw new GraphQlNoSuchEntityException(
                __('Product enquiry with ID %1 does not exist.', $enquiryId)
            );
        }
        return [
            'enquiry_id' => $enquiry->getEnquiryId(),
            'name' => $enquiry->getName(),
            'email' => $enquiry->getEmail(),
            'sku' => $enquiry->getSku(),
            'quantity' => $enquiry->getQuantity(),
            'created_at' => (string) $enquiry->getData('created_at'),
        ];
    }
}
