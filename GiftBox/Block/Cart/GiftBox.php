<?php

declare(strict_types=1);

namespace Codilar\GiftBox\Block\Cart;

use Codilar\GiftBox\Model\Quote\GiftBoxQuoteDataReader;
use Magento\Framework\View\Element\Template;
use Magento\Quote\Model\Quote\Item;
use Magento\Catalog\Api\ProductRepositoryInterface;

class GiftBox extends Template
{
    public function __construct(
        Template\Context $context,
        private readonly GiftBoxQuoteDataReader $giftBoxQuoteDataReader,
        private readonly ProductRepositoryInterface $productRepository,
        array $data = []
    ) {
        parent::__construct($context, $data);
    }

    private array $productNames = [];
    /**
     * @return array
     */
    public function getGiftBoxData(): array
    {
        $item = $this->getItem();
        if (!$item instanceof Item) {
            return [];
        }
        return $this->giftBoxQuoteDataReader->get($item);
    }

    /**
     * @return array
     */
    public function getCoffeeSkus(): array
    {
        return $this->getGiftBoxData()['coffee_skus'] ?? [];
    }

    /**
     * @return string|null
     */
    public function getEquipmentSku(): ?string
    {
        $sku = $this->getGiftBoxData()['equipment_sku'] ?? null;
        return $sku !== null && $sku !== ''
            ? (string) $sku
            : null;
    }

    /**
     * @return string|null
     */
    public function getGiftMessage(): ?string
    {
        $message = $this->getGiftBoxData()['gift_message'] ?? null;
        return $message !== null && $message !== ''
            ? (string) $message
            : null;
    }

    /**
     * @param string $sku
     * @return string
     */
    public function getProductName(string $sku): string
    {
        if (isset($this->productNames[$sku])) {
            return $this->productNames[$sku];
        }
        try {
            $name = (string) $this->productRepository
                ->get($sku)
                ->getName();
        } catch (\Magento\Framework\Exception\NoSuchEntityException) {
            $name = $sku;
        }
        $this->productNames[$sku] = $name;
        return $name;
    }
}
