<?php

declare(strict_types=1);

namespace Codilar\ProductEnquiry\Model\Resolver;

use Codilar\ProductEnquiry\Model\ProductEnquiryFactory;
use Codilar\ProductEnquiry\Api\ProductEnquiryRepositoryInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\GraphQl\Config\Element\Field;
use Magento\Framework\GraphQl\Exception\GraphQlInputException;
use Magento\Framework\GraphQl\Query\ResolverInterface;
use Magento\Framework\GraphQl\Schema\Type\ResolveInfo;

class CreateProductEnquiry implements ResolverInterface
{
    public function __construct(
        private readonly ProductEnquiryFactory $enquiryFactory,
        private readonly ProductEnquiryRepositoryInterface $enquiryRepository,
        private readonly ProductRepositoryInterface $productRepository
    ) {
    }
    public function resolve(
        Field $field,
        $context,
        ResolveInfo $info,
        ?array $value = null,
        ?array $args = null
    ): array {
        $input = $args['input'] ?? [];
        $name = trim($input['name'] ?? '');
        $email = trim($input['email'] ?? '');
        $sku = trim($input['sku'] ?? '');
        $quantity = (float) ($input['quantity'] ?? 0);
        if ($name === '') {
            throw new GraphQlInputException(
                __('Name is required.')
            );
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new GraphQlInputException(
                __('Please provide a valid email address.')
            );
        }
        if ($sku === '') {
            throw new GraphQlInputException(
                __('Product SKU is required.')
            );
        }
        if (!is_numeric($input['quantity'] ?? null) || $quantity <= 0) {
            throw new GraphQlInputException(
                __('Quantity must be greater than zero.')
            );
        }
        try {
            $this->productRepository->get($sku);
        } catch (NoSuchEntityException $exception) {
            throw new GraphQlInputException(
                __('No product exists with SKU "%1".', $sku)
            );
        }
        $enquiry = $this->enquiryFactory->create();
        $enquiry->setName($name);
        $enquiry->setEmail($email);
        $enquiry->setSku($sku);
        $enquiry->setQuantity($quantity);
        $savedEnquiry = $this->enquiryRepository->save($enquiry);
        $savedEnquiry = $this->enquiryRepository->getById(
            (int) $savedEnquiry->getEnquiryId()
        );
        return [
            'enquiry_id' => $savedEnquiry->getEnquiryId(),
            'name' => $savedEnquiry->getName(),
            'email' => $savedEnquiry->getEmail(),
            'sku' => $savedEnquiry->getSku(),
            'quantity' => $savedEnquiry->getQuantity(),
            'created_at' => (string) $savedEnquiry->getData('created_at'),
        ];
    }
}
